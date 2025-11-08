import { useEffect, useRef } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import PublicIcon from '@mui/icons-material/Public';
import WavesIcon from '@mui/icons-material/Waves';
import TyphoonIcon from '@mui/icons-material/Cyclone';
import LocalFireDepartmentIcon from '@mui/icons-material/LocalFireDepartment';

// Fix Leaflet default marker icon issue
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

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
  location: string;
}

export default function RealMap({
  earthquakes,
  disasters = []
}: {
  earthquakes: Earthquake[];
  disasters?: Disaster[];
}) {
  const mapRef = useRef<L.Map | null>(null);
  const mapContainerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!mapContainerRef.current || mapRef.current) return;

    // Initialize map centered on Philippines
    const map = L.map(mapContainerRef.current).setView([12.8797, 121.7740], 6);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19,
    }).addTo(map);

    mapRef.current = map;

    return () => {
      if (mapRef.current) {
        mapRef.current.remove();
        mapRef.current = null;
      }
    };
  }, []);

  useEffect(() => {
    if (!mapRef.current) return;

    const map = mapRef.current;

    // Clear existing markers
    map.eachLayer((layer) => {
      if (layer instanceof L.Marker || layer instanceof L.CircleMarker) {
        map.removeLayer(layer);
      }
    });

    // Add earthquake markers
    earthquakes.forEach((eq) => {
      const color = eq.magnitude >= 7 ? '#dc2626' :
                    eq.magnitude >= 6 ? '#f97316' :
                    eq.magnitude >= 5 ? '#eab308' :
                    eq.magnitude >= 4 ? '#fbbf24' : '#10b981';

      const circle = L.circleMarker([eq.latitude, eq.longitude], {
        radius: Math.max(5, eq.magnitude * 2),
        fillColor: color,
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.7,
      }).addTo(map);

      circle.bindPopup(`
        <div style="min-width: 200px;">
          <strong style="color: ${color}; font-size: 16px;">M${eq.magnitude.toFixed(1)}</strong><br/>
          <strong>${eq.location}</strong><br/>
          <small>${new Date(eq.occurred_at).toLocaleString()}</small>
        </div>
      `);
    });

    // Add disaster markers
    disasters.forEach((disaster) => {
      const color = disaster.severity === 'critical' ? '#dc2626' :
                    disaster.severity === 'high' ? '#f97316' :
                    disaster.severity === 'moderate' ? '#eab308' : '#10b981';

      const icon = disaster.type === 'flood' ? '🌊' :
                   disaster.type === 'typhoon' ? '🌀' :
                   disaster.type === 'fire' ? '🔥' : '⚠️';

      const customIcon = L.divIcon({
        className: 'custom-disaster-marker',
        html: `<div style="
          background: ${color};
          width: 32px;
          height: 32px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 18px;
          border: 2px solid white;
          box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        ">${icon}</div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
      });

      const marker = L.marker([disaster.latitude, disaster.longitude], {
        icon: customIcon,
      }).addTo(map);

      marker.bindPopup(`
        <div style="min-width: 200px;">
          <strong style="color: ${color}; font-size: 16px;">${icon} ${disaster.name}</strong><br/>
          <strong>${disaster.location}</strong><br/>
          <span style="text-transform: capitalize;">${disaster.type} - ${disaster.severity}</span>
        </div>
      `);
    });
  }, [earthquakes, disasters]);

  return (
    <div className="relative">
      <div
        ref={mapContainerRef}
        className="w-full h-96 rounded-lg z-0"
        style={{ minHeight: '400px' }}
      />

      {/* Legend */}
      <div className="mt-4 flex flex-wrap items-center justify-center gap-4 text-xs">
        <div className="flex items-center gap-2">
          <div className="w-3 h-3 rounded-full bg-green-500" />
          <span>M &lt; 4</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-3 h-3 rounded-full bg-yellow-500" />
          <span>M 4-5</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-3 h-3 rounded-full bg-orange-600" />
          <span>M 5-6</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-3 h-3 rounded-full bg-red-600" />
          <span>M &gt; 6</span>
        </div>
      </div>
    </div>
  );
}
