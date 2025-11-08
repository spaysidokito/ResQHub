import { useEffect, useRef } from 'react';

interface Earthquake {
  id: number;
  magnitude: number;
  location: string;
  latitude: number;
  longitude: number;
  occurred_at: string;
}

interface Disaster {
  id: number;
  type: string;
  name: string;
  latitude: number;
  longitude: number;
  severity: string;
}

export default function EarthquakeMap({
  earthquakes,
  disasters = []
}: {
  earthquakes: Earthquake[];
  disasters?: Disaster[];
}) {
  const mapRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    // Simple map visualization - in production, use Leaflet or Google Maps
    if (mapRef.current) {
      renderMap();
    }
  }, [earthquakes]);

  const renderMap = () => {
    // Placeholder for map rendering
    // In production, integrate with Leaflet.js or Google Maps API
  };

  const getMagnitudeColor = (magnitude: number) => {
    if (magnitude >= 7) return 'bg-red-600';
    if (magnitude >= 6) return 'bg-orange-600';
    if (magnitude >= 5) return 'bg-yellow-600';
    if (magnitude >= 4) return 'bg-yellow-500';
    return 'bg-green-500';
  };

  return (
    <div className="relative">
      <div
        ref={mapRef}
        className="w-full h-96 bg-gray-800 rounded-lg flex items-center justify-center relative overflow-hidden"
      >
        {/* Placeholder map */}
        <div className="absolute inset-0 bg-gradient-to-br from-gray-900 to-gray-800">
          <div className="absolute inset-0 opacity-20">
            <svg className="w-full h-full" viewBox="0 0 100 100">
              <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" strokeWidth="0.5" />
              <circle cx="50" cy="50" r="30" fill="none" stroke="#ef4444" strokeWidth="0.5" />
              <circle cx="50" cy="50" r="20" fill="none" stroke="#ef4444" strokeWidth="0.5" />
              <circle cx="50" cy="50" r="10" fill="none" stroke="#ef4444" strokeWidth="0.5" />
            </svg>
          </div>
        </div>

        {/* Earthquake markers */}
        <div className="absolute inset-0">
          {earthquakes.slice(0, 20).map((eq) => {
            const x = ((eq.longitude + 180) / 360) * 100;
            const y = ((90 - eq.latitude) / 180) * 100;

            return (
              <div
                key={`eq-${eq.id}`}
                className="absolute transform -translate-x-1/2 -translate-y-1/2 group"
                style={{ left: `${x}%`, top: `${y}%` }}
              >
                <div className={`w-3 h-3 rounded-full ${getMagnitudeColor(eq.magnitude)} animate-pulse cursor-pointer`} />
                <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                  <div className="bg-black border border-red-600 rounded px-3 py-2 text-xs whitespace-nowrap">
                    <div className="font-bold text-red-500">🌍 M{eq.magnitude}</div>
                    <div className="text-gray-300">{eq.location}</div>
                    <div className="text-gray-500 text-xs">
                      {new Date(eq.occurred_at).toLocaleString()}
                    </div>
                  </div>
                </div>
              </div>
            );
          })}

          {/* Disaster markers */}
          {disasters.slice(0, 20).map((disaster) => {
            const x = ((disaster.longitude + 180) / 360) * 100;
            const y = ((90 - disaster.latitude) / 180) * 100;
            const icon = disaster.type === 'flood' ? '🌊' : disaster.type === 'typhoon' ? '🌀' : disaster.type === 'fire' ? '🔥' : '⚠️';
            const color = disaster.severity === 'critical' ? 'bg-red-600' : disaster.severity === 'high' ? 'bg-orange-600' : disaster.severity === 'moderate' ? 'bg-yellow-600' : 'bg-green-500';

            return (
              <div
                key={`dis-${disaster.id}`}
                className="absolute transform -translate-x-1/2 -translate-y-1/2 group"
                style={{ left: `${x}%`, top: `${y}%` }}
              >
                <div className={`w-6 h-6 rounded-full ${color} flex items-center justify-center animate-pulse cursor-pointer text-xs`}>
                  {icon}
                </div>
                <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                  <div className="bg-black border border-red-600 rounded px-3 py-2 text-xs whitespace-nowrap">
                    <div className="font-bold text-red-500">{icon} {disaster.name}</div>
                    <div className="text-gray-300 capitalize">{disaster.type} - {disaster.severity}</div>
                  </div>
                </div>
              </div>
            );
          })}
        </div>

        <div className="relative z-10 text-center text-gray-500">
          <p className="text-sm">Interactive Map - Philippines</p>
          <p className="text-xs mt-1">{earthquakes.length} earthquakes, {disasters.length} disasters</p>
        </div>
      </div>

      {/* Legend */}
      <div className="mt-4 flex items-center justify-center space-x-4 text-xs">
        <div className="flex items-center space-x-2">
          <div className="w-3 h-3 rounded-full bg-green-500" />
          <span>M &lt; 4</span>
        </div>
        <div className="flex items-center space-x-2">
          <div className="w-3 h-3 rounded-full bg-yellow-500" />
          <span>M 4-5</span>
        </div>
        <div className="flex items-center space-x-2">
          <div className="w-3 h-3 rounded-full bg-orange-600" />
          <span>M 5-6</span>
        </div>
        <div className="flex items-center space-x-2">
          <div className="w-3 h-3 rounded-full bg-red-600" />
          <span>M &gt; 6</span>
        </div>
      </div>
    </div>
  );
}
