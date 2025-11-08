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
  const [locationSearch, setLocationSearch] = useState('');
  const [locationSuggestions, setLocationSuggestions] = useState<any[]>([]);
  const [isSearching, setIsSearching] = useState(false);
  const [showSuggestions, setShowSuggestions] = useState(false);

  useEffect(() => {
    fetchPreferences();
  }, []);

  useEffect(() => {
    setLocationSearch(preferences.location_name);
  }, [preferences.location_name]);

  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      const target = e.target as HTMLElement;
      if (!target.closest('.location-autocomplete')) {
        setShowSuggestions(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
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

      if (onSave) {
        onSave();
      }

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

  const searchLocation = async (query: string) => {
    if (query.length < 3) {
      setLocationSuggestions([]);
      return;
    }

    setIsSearching(true);
    try {

      const response = await fetch(
        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1`,
        {
          headers: {
            'User-Agent': 'ResQHub Disaster Management System',
          },
        }
      );

      if (response.ok) {
        const data = await response.json();
        setLocationSuggestions(data);
        setShowSuggestions(true);
      }
    } catch (error) {
      console.error('Error searching location:', error);
    } finally {
      setIsSearching(false);
    }
  };

  const selectLocation = (location: any) => {
    setPreferences({
      ...preferences,
      latitude: parseFloat(location.lat),
      longitude: parseFloat(location.lon),
      location_name: location.display_name,
    });
    setLocationSearch(location.display_name);
    setShowSuggestions(false);
    setLocationSuggestions([]);
  };

  const useCurrentLocation = () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        async (position) => {

          try {
            const response = await fetch(
              `https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`,
              {
                headers: {
                  'User-Agent': 'ResQHub Disaster Management System',
                },
              }
            );

            if (response.ok) {
              const data = await response.json();
              setPreferences({
                ...preferences,
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                location_name: data.display_name || 'Current Location',
              });
              setLocationSearch(data.display_name || 'Current Location');
            }
          } catch (error) {
            console.error('Error reverse geocoding:', error);
            setPreferences({
              ...preferences,
              latitude: position.coords.latitude,
              longitude: position.coords.longitude,
              location_name: 'Current Location',
            });
            setLocationSearch('Current Location');
          }
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
        {}
        <div className="bg-gradient-to-r from-red-900 to-black px-6 py-4 border-b-2 border-red-600 flex items-center justify-between sticky top-0">
          <h2 className="text-2xl font-bold text-red-500">Settings</h2>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-white transition"
          >
            <CloseIcon />
          </button>
        </div>

        {}
        <div className="p-6 space-y-6">
          {}
          <div>
            <h3 className="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <LocationOnIcon /> Location Settings
            </h3>
            <div className="space-y-4">
              <div className="relative location-autocomplete">
                <label className="block text-sm text-gray-400 mb-2">Search Location</label>
                <input
                  type="text"
                  value={locationSearch || preferences.location_name}
                  onChange={(e) => {
                    setLocationSearch(e.target.value);
                    searchLocation(e.target.value);
                  }}
                  onFocus={() => locationSuggestions.length > 0 && setShowSuggestions(true)}
                  placeholder="Type to search for a location..."
                  className="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-600"
                />
                {isSearching && (
                  <div className="absolute right-3 top-10 text-gray-400">
                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-red-500"></div>
                  </div>
                )}

                {}
                {showSuggestions && locationSuggestions.length > 0 && (
                  <div className="absolute z-50 w-full mt-1 bg-gray-800 border-2 border-red-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    {locationSuggestions.map((suggestion, index) => (
                      <button
                        key={index}
                        type="button"
                        onClick={() => selectLocation(suggestion)}
                        className="w-full text-left px-4 py-3 hover:bg-gray-700 transition border-b border-gray-700 last:border-b-0"
                      >
                        <div className="flex items-start gap-2">
                          <LocationOnIcon sx={{ fontSize: 18, color: '#ef4444' }} className="mt-0.5" />
                          <div className="flex-1">
                            <div className="text-white text-sm">{suggestion.display_name}</div>
                            <div className="text-gray-400 text-xs mt-1">
                              {suggestion.lat.substring(0, 8)}, {suggestion.lon.substring(0, 8)}
                            </div>
                          </div>
                        </div>
                      </button>
                    ))}
                  </div>
                )}
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

          {}
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

          {}
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

          {}
          {message && (
            <div className={`p-4 rounded-lg ${message.includes('Error') ? 'bg-red-900 border border-red-700' : 'bg-green-900 border border-green-700'}`}>
              <p className="text-white text-sm">{message}</p>
            </div>
          )}

          {}
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
