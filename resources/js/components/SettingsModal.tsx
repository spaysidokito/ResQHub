import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import LocationOnIcon from '@mui/icons-material/LocationOn';
import NotificationsIcon from '@mui/icons-material/Notifications';
import VolumeUpIcon from '@mui/icons-material/VolumeUp';
import MyLocationIcon from '@mui/icons-material/MyLocation';
import CloseIcon from '@mui/icons-material/Close';

interface Preferences {
  latitude: number;
  longitude: number;
  location_name: string;
  radius_km: number;
  min_magnitude: number;
  push_alerts: boolean;
  sound_alerts: boolean;
}

export default function SettingsModal({ onClose, onSave }: { onClose: () => void; onSave?: () => void }) {
  const [preferences, setPreferences] = useState<Preferences>({
    latitude: 14.5995,
    longitude: 120.9842,
    location_name: 'Manila, Philippines',
    radius_km: 100,
    min_magnitude: 3.0,
    push_alerts: true,
    sound_alerts: true,
  });
  const [isSaving, setIsSaving] = useState(false);
  const [message, setMessage] = useState('');

  useEffect(() => {
    fetchPreferences();
  }, []);

  const fetchPreferences = async () => {
    try {
      const response = await fetch('/api/preferences');
      const data = await response.json();
      if (data) {
        setPreferences(data);
      }
    } catch (error) {
      console.error('Error fetching preferences:', error);
    }
  };

  const savePreferences = async () => {
    setIsSaving(true);
    setMessage('');

    try {
      const response = await fetch('/api/preferences', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify(preferences),
      });

      const data = await response.json();
      setMessage('Settings saved successfully!');

      // Call onSave callback to refresh user location on parent component
      if (onSave) {
        onSave();
      }

      // Close modal after a short delay to show success message
      setTimeout(() => {
        onClose();
      }, 1000);
    } catch (error) {
      console.error('Error saving preferences:', error);
      setMessage('Error saving settings. Please try again.');
    } finally {
      setIsSaving(false);
    }
  };

  const useCurrentLocation = () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          setPreferences({
            ...preferences,
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            location_name: 'Current Location',
          });
        },
        (error) => {
          console.error('Error getting location:', error);
          alert('Unable to get your location. Please enter manually.');
        }
      );
    } else {
      alert('Geolocation is not supported by your browser.');
    }
  };

  return (
    <motion.div
      className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      transition={{ duration: 0.2 }}
      onClick={onClose}
    >
      <motion.div
        className="bg-gray-900 border-2 border-red-600 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        initial={{ scale: 0.9, y: 20 }}
        animate={{ scale: 1, y: 0 }}
        exit={{ scale: 0.9, y: 20 }}
        transition={{ duration: 0.3 }}
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="bg-gradient-to-r from-red-900 to-black px-6 py-4 border-b-2 border-red-600 flex items-center justify-between sticky top-0">
          <h2 className="text-2xl font-bold text-red-500">Settings</h2>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-white transition"
          >
            <CloseIcon />
          </button>
        </div>

        {/* Content */}
        <div className="p-6 space-y-6">
          {/* Location Settings */}
          <div>
            <h3 className="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <LocationOnIcon /> Location Settings
            </h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm text-gray-400 mb-2">Location Name</label>
                <input
                  type="text"
                  value={preferences.location_name}
                  onChange={(e) => setPreferences({ ...preferences, location_name: e.target.value })}
                  className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-600"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm text-gray-400 mb-2">Latitude</label>
                  <input
                    type="number"
                    step="0.0001"
                    value={preferences.latitude}
                    onChange={(e) => setPreferences({ ...preferences, latitude: parseFloat(e.target.value) })}
                    className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-600"
                  />
                </div>
                <div>
                  <label className="block text-sm text-gray-400 mb-2">Longitude</label>
                  <input
                    type="number"
                    step="0.0001"
                    value={preferences.longitude}
                    onChange={(e) => setPreferences({ ...preferences, longitude: parseFloat(e.target.value) })}
                    className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-600"
                  />
                </div>
              </div>

              <button
                onClick={useCurrentLocation}
                className="w-full px-4 py-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg transition flex items-center justify-center gap-2"
              >
                <MyLocationIcon /> Use Current Location
              </button>
            </div>
          </div>

          {/* Alert Settings */}
          <div>
            <h3 className="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <NotificationsIcon /> Alert Settings
            </h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm text-gray-400 mb-2">
                  Alert Radius: {preferences.radius_km} km
                </label>
                <input
                  type="range"
                  min="10"
                  max="500"
                  step="10"
                  value={preferences.radius_km}
                  onChange={(e) => setPreferences({ ...preferences, radius_km: parseInt(e.target.value) })}
                  className="w-full"
                />
              </div>

              <div>
                <label className="block text-sm text-gray-400 mb-2">
                  Minimum Magnitude: {preferences.min_magnitude.toFixed(1)}
                </label>
                <input
                  type="range"
                  min="1"
                  max="9"
                  step="0.1"
                  value={preferences.min_magnitude}
                  onChange={(e) => setPreferences({ ...preferences, min_magnitude: parseFloat(e.target.value) })}
                  className="w-full"
                />
              </div>
            </div>
          </div>

          {/* Notification Preferences */}
          <div>
            <h3 className="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <VolumeUpIcon /> Notification Preferences
            </h3>
            <div className="space-y-3">
              <label className="flex items-center space-x-3 cursor-pointer">
                <input
                  type="checkbox"
                  checked={preferences.sound_alerts}
                  onChange={(e) => setPreferences({ ...preferences, sound_alerts: e.target.checked })}
                  className="w-5 h-5 text-red-600 bg-gray-800 border-gray-700 rounded focus:ring-red-600"
                />
                <span className="text-white">Sound Alerts</span>
              </label>
            </div>
          </div>

          {/* Message */}
          {message && (
            <div className={`p-4 rounded-lg ${message.includes('Error') ? 'bg-red-900 border border-red-700' : 'bg-green-900 border border-green-700'}`}>
              <p className="text-white text-sm">{message}</p>
            </div>
          )}

          {/* Actions */}
          <div className="flex space-x-4">
            <button
              onClick={savePreferences}
              disabled={isSaving}
              className="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 disabled:bg-gray-700 disabled:cursor-not-allowed rounded-lg font-semibold transition"
            >
              {isSaving ? 'Saving...' : 'Save Settings'}
            </button>
            <button
              onClick={onClose}
              className="px-6 py-3 bg-gray-800 hover:bg-gray-700 rounded-lg transition"
            >
              Cancel
            </button>
          </div>
        </div>
      </motion.div>
    </motion.div>
  );
}
