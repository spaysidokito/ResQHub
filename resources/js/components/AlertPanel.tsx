import { useState, useEffect } from 'react';

interface Alert {
  id: number;
  title: string;
  message: string;
  severity: string;
  sent_at: string;
  is_read: boolean;
}

export default function AlertPanel({ onUnreadChange }: { onUnreadChange: (count: number) => void }) {
  const [alerts, setAlerts] = useState<Alert[]>([]);
  const [showAll, setShowAll] = useState(false);
  const [seenAlertIds, setSeenAlertIds] = useState<Set<number>>(new Set());
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [audioContext, setAudioContext] = useState<AudioContext | null>(null);

  useEffect(() => {
    // Initialize audio context on user interaction (required for mobile)
    const initAudio = () => {
      if (!audioContext) {
        const ctx = new (window.AudioContext || (window as any).webkitAudioContext)();
        setAudioContext(ctx);
      }
    };

    // Add click listener to initialize audio
    document.addEventListener('click', initAudio, { once: true });
    document.addEventListener('touchstart', initAudio, { once: true });

    // Initial fetch (don't notify on first load)
    fetchAlerts(true);

    // Auto-refresh every 5 seconds (notify on subsequent loads)
    const interval = setInterval(() => {
      fetchAlerts(false);
    }, 5000);

    // Cleanup
    return () => {
      clearInterval(interval);
      document.removeEventListener('click', initAudio);
      document.removeEventListener('touchstart', initAudio);
    };
  }, []);



  const fetchAlerts = async (isInitialLoad = false) => {
    try {
      const response = await fetch('/api/alerts');
      const data = await response.json();

      // On initial load, just mark all as seen without notifying
      if (isInitialLoad || seenAlertIds.size === 0) {
        const initialSeenIds = new Set(data.map((a: Alert) => a.id));
        setSeenAlertIds(initialSeenIds);
        setAlerts(data);
        const unreadCount = data.filter((a: Alert) => !a.is_read).length;
        onUnreadChange(unreadCount);
        console.log('Initial load: marked', initialSeenIds.size, 'alerts as seen');
        return;
      }

      // Find truly new alerts (not seen before)
      const newAlerts = data.filter((alert: Alert) => !seenAlertIds.has(alert.id));

      console.log('Checking for new alerts:', {
        totalAlerts: data.length,
        seenCount: seenAlertIds.size,
        newAlertsCount: newAlerts.length,
        newAlertIds: newAlerts.map(a => a.id)
      });

      // Only notify for new unread alerts
      if (newAlerts.length > 0) {
        const newUnreadAlerts = newAlerts.filter((a: Alert) => !a.is_read);
        console.log('New unread alerts:', newUnreadAlerts.length);

        if (newUnreadAlerts.length > 0) {
          // Show notification only for the newest alert
          console.log('Showing notification for:', newUnreadAlerts[0].title);
          showNewAlertNotification(newUnreadAlerts[0]);
        }
      }

      // Update seen alert IDs
      const newSeenIds = new Set(data.map((a: Alert) => a.id));
      setSeenAlertIds(newSeenIds);

      setAlerts(data);
      const unreadCount = data.filter((a: Alert) => !a.is_read).length;
      onUnreadChange(unreadCount);
    } catch (error) {
      console.error('Error fetching alerts:', error);
    }
  };

  const showNewAlertNotification = (alert: Alert) => {
    console.log('New alert detected:', alert.title);

    // Play sound
    playNotificationSound();

    // Vibrate on mobile devices
    if ('vibrate' in navigator) {
      try {
        // Vibration pattern: vibrate 200ms, pause 100ms, vibrate 200ms
        navigator.vibrate([200, 100, 200]);
        console.log('Vibration triggered');
      } catch (e) {
        console.log('Vibration error:', e);
      }
    }
  };

  const playNotificationSound = () => {
    if (!audioContext) {
      console.log('Audio context not initialized');
      return;
    }

    try {
      // Resume audio context if suspended (mobile requirement)
      if (audioContext.state === 'suspended') {
        audioContext.resume();
      }

      // Use Web Audio API to generate beep sound (works best on mobile)
      playBeepSound(audioContext);
      console.log('Sound played');
    } catch (e) {
      console.log('Audio error:', e);
    }
  };

  const playBeepSound = (ctx: AudioContext) => {
    try {
      // Generate a beep sound using Web Audio API (works on most mobile devices)
      const oscillator = ctx.createOscillator();
      const gainNode = ctx.createGain();

      oscillator.connect(gainNode);
      gainNode.connect(ctx.destination);

      oscillator.frequency.value = 800; // Frequency in Hz
      oscillator.type = 'sine';

      gainNode.gain.setValueAtTime(0.5, ctx.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);

      oscillator.start(ctx.currentTime);
      oscillator.stop(ctx.currentTime + 0.3);
    } catch (e) {
      console.log('Beep sound error:', e);
    }
  };

  const markAsRead = async (alertId: number) => {
    try {
      await fetch(`/api/alerts/${alertId}/read`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      fetchAlerts();
    } catch (error) {
      console.error('Error marking alert as read:', error);
    }
  };

  const markAllAsRead = async () => {
    try {
      await fetch('/api/alerts/read-all', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      fetchAlerts();
    } catch (error) {
      console.error('Error marking all alerts as read:', error);
    }
  };

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'critical':
        return 'bg-red-600 border-red-700';
      case 'high':
        return 'bg-orange-600 border-orange-700';
      case 'moderate':
        return 'bg-yellow-600 border-yellow-700';
      default:
        return 'bg-green-600 border-green-700';
    }
  };

  const displayedAlerts = showAll ? alerts : alerts.slice(0, 5);

  return (
    <div className="bg-gray-900 rounded-lg border-2 border-red-600 p-6">
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center gap-2">
          <h3 className="text-xl font-bold text-red-500">Alerts</h3>
          <span className="text-xs text-gray-500">(Auto-updates every 5s)</span>
        </div>
        <div className="flex items-center gap-2">
          {'Notification' in window && Notification.permission === 'default' && (
            <button
              onClick={requestNotificationPermission}
              className="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition animate-pulse"
              title="Enable sound and vibration alerts"
            >
              🔔 Enable Notifications
            </button>
          )}
          {alerts.some(a => !a.is_read) && (
            <button
              onClick={markAllAsRead}
              className="text-xs text-gray-400 hover:text-white transition"
            >
              Mark all read
            </button>
          )}
        </div>
      </div>



      <div className="space-y-3 max-h-96 overflow-y-auto">
        {displayedAlerts.length === 0 ? (
          <div className="text-center text-gray-500 py-4">
            No alerts yet
          </div>
        ) : (
          displayedAlerts.map((alert) => (
            <div
              key={alert.id}
              className={`p-3 rounded-lg border ${
                alert.is_read ? 'bg-gray-800 border-gray-700' : getSeverityColor(alert.severity)
              } cursor-pointer transition hover:opacity-80`}
              onClick={() => !alert.is_read && markAsRead(alert.id)}
            >
              <div className="flex items-start justify-between">
                <div className="flex-1">
                  <h4 className="font-semibold text-sm">{alert.title}</h4>
                  <p className="text-xs text-gray-300 mt-1">{alert.message}</p>
                  <p className="text-xs text-gray-500 mt-2">
                    {new Date(alert.sent_at).toLocaleString()}
                  </p>
                </div>
                {!alert.is_read && (
                  <div className="w-2 h-2 bg-white rounded-full ml-2 mt-1" />
                )}
              </div>
            </div>
          ))
        )}
      </div>

      {alerts.length > 5 && (
        <button
          onClick={() => setShowAll(!showAll)}
          className="w-full mt-3 text-sm text-red-500 hover:text-red-400 transition"
        >
          {showAll ? 'Show less' : `Show all (${alerts.length})`}
        </button>
      )}
    </div>
  );
}
