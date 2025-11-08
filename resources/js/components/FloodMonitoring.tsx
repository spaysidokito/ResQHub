import { useState, useEffect } from 'react';
import WavesIcon from '@mui/icons-material/Waves';
import TrendingUpIcon from '@mui/icons-material/TrendingUp';
import TrendingDownIcon from '@mui/icons-material/TrendingDown';
import WarningIcon from '@mui/icons-material/Warning';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';
import InfoIcon from '@mui/icons-material/Info';

interface WaterLevelStation {
  id: number;
  name: string;
  location: string;
  waterLevel: number; // in meters
  status: 'normal' | 'alert' | 'critical' | 'unavailable';
  trend: 'rising' | 'falling' | 'stable';
  lastUpdate: string;
  criticalLevel: number;
  alertLevel: number;
}

export default function FloodMonitoring() {
  const [stations, setStations] = useState<WaterLevelStation[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [lastFetch, setLastFetch] = useState<Date>(new Date());
  const [initialStations] = useState<WaterLevelStation[]>([
    {
      id: 1,
      name: 'Angono',
      location: 'Laguna (Rizal)',
      waterLevel: 12.89,
      status: 'normal',
      trend: 'stable',
      lastUpdate: '11/8/2025, 12:30:01 PM',
      criticalLevel: 15.0,
      alertLevel: 13.5,
    },
    {
      id: 2,
      name: 'Burgos',
      location: 'Rizal (Rizal)',
      waterLevel: 27.23,
      status: 'normal',
      trend: 'falling',
      lastUpdate: '11/8/2025, 12:30:01 PM',
      criticalLevel: 30.0,
      alertLevel: 28.0,
    },
    {
      id: 3,
      name: 'La Mesa Dam',
      location: 'Quezon City',
      waterLevel: 78.72,
      status: 'normal',
      trend: 'stable',
      lastUpdate: '11/8/2025, 12:30:01 PM',
      criticalLevel: 80.15,
      alertLevel: 79.0,
    },
    {
      id: 4,
      name: 'Mindanao',
      location: 'Various',
      waterLevel: 0,
      status: 'unavailable',
      trend: 'stable',
      lastUpdate: '11/8/2025, 12:30:01 PM',
      criticalLevel: 10.0,
      alertLevel: 8.0,
    },
  ]);

  const [selectedStation, setSelectedStation] = useState<WaterLevelStation | null>(null);

  useEffect(() => {
    fetchWaterLevels();
  }, []);

  useEffect(() => {
    if (stations.length > 0 && !selectedStation) {
      setSelectedStation(stations[2] || stations[0]);
    }
  }, [stations]);

  const fetchWaterLevels = async () => {
    setIsLoading(true);
    try {
      const response = await fetch('/api/water-levels');
      if (response.ok) {
        const data = await response.json();
        if (data.stations && data.stations.length > 0) {
          setStations(data.stations);
        } else {
          setStations(initialStations);
        }
      } else {
        setStations(initialStations);
      }
      setLastFetch(new Date());
    } catch (error) {
      console.error('Error fetching water levels:', error);
      setStations(initialStations);
    } finally {
      setIsLoading(false);
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'critical':
        return 'text-red-500 bg-red-500/10 border-red-500';
      case 'alert':
        return 'text-yellow-500 bg-yellow-500/10 border-yellow-500';
      case 'normal':
        return 'text-green-500 bg-green-500/10 border-green-500';
      case 'unavailable':
        return 'text-gray-500 bg-gray-500/10 border-gray-500';
      default:
        return 'text-gray-500 bg-gray-500/10 border-gray-500';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'critical':
        return <WarningIcon sx={{ fontSize: 16 }} />;
      case 'alert':
        return <InfoIcon sx={{ fontSize: 16 }} />;
      case 'normal':
        return <CheckCircleIcon sx={{ fontSize: 16 }} />;
      case 'unavailable':
        return <InfoIcon sx={{ fontSize: 16 }} />;
      default:
        return <InfoIcon sx={{ fontSize: 16 }} />;
    }
  };

  const getTrendIcon = (trend: string) => {
    switch (trend) {
      case 'rising':
        return <TrendingUpIcon sx={{ fontSize: 16 }} className="text-red-400" />;
      case 'falling':
        return <TrendingDownIcon sx={{ fontSize: 16 }} className="text-green-400" />;
      default:
        return <span className="text-gray-400 text-xs">—</span>;
    }
  };

  const getWaterLevelPercentage = (station: WaterLevelStation) => {
    if (station.status === 'unavailable') return 0;
    const minLevel = station.alertLevel * 0.7;
    const range = station.criticalLevel - minLevel;
    const currentFromMin = station.waterLevel - minLevel;
    return Math.min(Math.max((currentFromMin / range) * 100, 0), 100);
  };

  const getBarColor = (station: WaterLevelStation) => {
    if (station.status === 'critical') return 'bg-red-500';
    if (station.status === 'alert') return 'bg-yellow-500';
    return 'bg-green-500';
  };

  return (
    <div className="bg-gray-900 rounded-lg border-2 border-red-600 p-4 md:p-6">
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center gap-2">
          <WavesIcon className="text-blue-400" sx={{ fontSize: 24 }} />
          <h3 className="text-lg md:text-xl font-bold text-red-500">Flood Monitoring</h3>
        </div>
        <button
          onClick={fetchWaterLevels}
          disabled={isLoading}
          className="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-700 disabled:cursor-not-allowed rounded-lg transition text-xs flex items-center gap-1"
          title="Refresh water levels"
        >
          <svg
            className={`w-4 h-4 ${isLoading ? 'animate-spin' : ''}`}
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          <span className="hidden sm:inline">{isLoading ? 'Refreshing...' : 'Refresh'}</span>
        </button>
      </div>

      <div className="text-xs text-gray-400 mb-3">
        Last updated: {lastFetch.toLocaleTimeString()}
      </div>

      {}
      {selectedStation && selectedStation.status !== 'unavailable' && (
        <div className="bg-blue-600 rounded-lg p-4 mb-4">
          <div className="text-center">
            <p className="text-blue-100 text-sm mb-1">{selectedStation.name}</p>
            <p className="text-white text-4xl font-bold mb-1">{selectedStation.waterLevel.toFixed(2)}m</p>
            <p className="text-blue-200 text-xs">Water Level</p>
          </div>

          {}
          <div className="mt-4 bg-blue-800 rounded-full h-3 overflow-hidden">
            <div
              className={`h-full transition-all duration-500 ${getBarColor(selectedStation)}`}
              style={{ width: `${getWaterLevelPercentage(selectedStation)}%` }}
            />
          </div>

          {}
          <div className="grid grid-cols-3 gap-2 mt-3 text-xs">
            <div className="text-center">
              <p className="text-blue-200">Normal</p>
              <p className="text-white font-semibold">&lt; {selectedStation.alertLevel}m</p>
            </div>
            <div className="text-center">
              <p className="text-yellow-300">Alert</p>
              <p className="text-white font-semibold">{selectedStation.alertLevel}m</p>
            </div>
            <div className="text-center">
              <p className="text-red-300">Critical</p>
              <p className="text-white font-semibold">{selectedStation.criticalLevel}m</p>
            </div>
          </div>
        </div>
      )}

      {}
      <div className="space-y-2">
        <p className="text-gray-400 text-xs mb-2">Monitoring Stations:</p>
        {stations.map((station) => (
          <button
            key={station.id}
            onClick={() => setSelectedStation(station)}
            className={`w-full text-left p-3 rounded-lg border transition ${
              selectedStation?.id === station.id
                ? 'bg-gray-800 border-red-500'
                : 'bg-gray-800/50 border-gray-700 hover:border-gray-600'
            }`}
          >
            <div className="flex items-start justify-between mb-1">
              <div className="flex items-center gap-2">
                <WavesIcon className="text-blue-400" sx={{ fontSize: 16 }} />
                <span className="text-white font-semibold text-sm">{station.name}</span>
              </div>
              <div className={`flex items-center gap-1 px-2 py-0.5 rounded text-xs border ${getStatusColor(station.status)}`}>
                {getStatusIcon(station.status)}
                <span className="capitalize">{station.status}</span>
              </div>
            </div>

            <p className="text-gray-400 text-xs mb-2">{station.location}</p>

            {station.status !== 'unavailable' ? (
              <>
                <div className="flex items-center justify-between text-xs">
                  <span className="text-gray-400">Water Level:</span>
                  <div className="flex items-center gap-1">
                    <span className="text-white font-bold">{station.waterLevel.toFixed(2)}m</span>
                    {getTrendIcon(station.trend)}
                  </div>
                </div>

                {}
                <div className="mt-2 bg-gray-700 rounded-full h-1.5 overflow-hidden">
                  <div
                    className={`h-full transition-all ${getBarColor(station)}`}
                    style={{ width: `${getWaterLevelPercentage(station)}%` }}
                  />
                </div>
              </>
            ) : (
              <p className="text-gray-500 text-xs italic">Data Unavailable</p>
            )}

            <p className="text-gray-500 text-xs mt-2">Updated: {station.lastUpdate}</p>
          </button>
        ))}
      </div>

      {}
      <div className="mt-4 pt-4 border-t border-gray-800">
        <p className="text-gray-500 text-xs text-center">
          Data from{' '}
          <a
            href="https://pagasa.dost.gov.ph/"
            target="_blank"
            rel="noopener noreferrer"
            className="text-blue-400 hover:text-blue-300 underline"
          >
            PAGASA
          </a>
        </p>
      </div>
    </div>
  );
}
