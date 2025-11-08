import { useEffect, useRef } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import PublicIcon from '@mui/icons-material/Public';
import WavesIcon from '@mui/icons-material/Waves';
import TyphoonIcon from '@mui/icons-material/Cyclone';
import LocalFireDepartmentIcon from '@mui/icons-material/LocalFireDepartment';

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

interface UserLocation {
  latitude: number;
  longitude: number;
  location_name: string;
  radius_km: number;
}

export default function RealMap({
  earthquakes,
  disasters = [],
  userLocation
}: {
  earthquakes: Earthquake[];
  disasters?: Disaster[];
  userLocation?: UserLocation;
}) {
  const mapRef = useRef<L.Map | null>(null);
  const mapContainerRef = useRef<HTMLDivElement>(null);
  const radiusCircleRef = useRef<L.Circle | null>(null);
  const userMarkerRef = useRef<L.Marker | null>(null);

  useEffect(() => {
    if (!mapContainerRef.current || mapRef.current) return;

    const map = L.map(mapContainerRef.current).setView([12.8797, 121.7740], 6);

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

    map.eachLayer((layer) => {
      if ((layer instanceof L.Marker || layer instanceof L.CircleMarker) &&
          layer !== userMarkerRef.current &&
          layer !== radiusCircleRef.current) {
        map.removeLayer(layer);
      }
    });

    earthquakes.forEach((eq) => {
      const color = eq.magnitude >= 6 ? '#dc2626' :
                    eq.magnitude >= 5 ? '#ea580c' :
                    eq.magnitude >= 4 ? '#eab308' : '#10b981';

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

  useEffect(() => {
    console.log('RealMap userLocation effect triggered:', userLocation);
    if (!mapRef.current || !userLocation) {
      console.log('Skipping user location render:', { hasMap: !!mapRef.current, hasLocation: !!userLocation });
      return;
    }

    const map = mapRef.current;
    console.log('Drawing user location and radius circle');

    if (radiusCircleRef.current) {
      map.removeLayer(radiusCircleRef.current);
    }
    if (userMarkerRef.current) {
      map.removeLayer(userMarkerRef.current);
    }

    console.log('Creating circle at:', userLocation.latitude, userLocation.longitude, 'with radius:', userLocation.radius_km * 1000, 'meters');
    const circle = L.circle([userLocation.latitude, userLocation.longitude], {
      radius: userLocation.radius_km * 1000, // Convert km to meters
      color: '#3b82f6',
      fillColor: '#3b82f6',
      fillOpacity: 0.1,
      weight: 2,
      dashArray: '5, 10',
    }).addTo(map);

    console.log('Circle created and added to map');
    radiusCircleRef.current = circle;

    const userIcon = L.divIcon({
      className: 'custom-user-marker',
      html: `<div style="
        background: #3b82f6;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.4);
      ">
        <div style="
          width: 8px;
          height: 8px;
          background: white;
          border-radius: 50%;
        "></div>
      </div>`,
      iconSize: [24, 24],
      iconAnchor: [12, 12],
    });

    console.log('Creating user marker at:', userLocation.latitude, userLocation.longitude);
    const marker = L.marker([userLocation.latitude, userLocation.longitude], {
      icon: userIcon,
    }).addTo(map);

    marker.bindPopup(`
      <div style="min-width: 200px;">
        <strong style="color: #3b82f6; font-size: 16px;">📍 Your Location</strong><br/>
        <strong>${userLocation.location_name}</strong><br/>
        <span>Alert Radius: ${userLocation.radius_km} km</span>
      </div>
    `);

    console.log('User marker created and added to map');
    userMarkerRef.current = marker;

    console.log('Centering map on user location');
    map.setView([userLocation.latitude, userLocation.longitude], 8);
    console.log('Map centered');

  }, [userLocation]);

  return (
    <div className="relative">
      <div
        ref={mapContainerRef}
        className="w-full h-96 rounded-lg z-0"
        style={{ minHeight: '400px' }}
      />

      {}
      {process.env.NODE_ENV === 'development' && (
        <div className="mt-2 p-2 bg-gray-800 rounded text-xs">
          <strong>Debug:</strong> userLocation = {userLocation ? JSON.stringify(userLocation) : 'null'}
        </div>
      )}

      {}
      <div className="mt-4 flex flex-wrap items-center justify-center gap-4 text-xs">
        {userLocation && (
          <div className="flex items-center gap-2 px-3 py-1 bg-blue-900/30 border border-blue-600 rounded">
            <div className="w-3 h-3 rounded-full bg-blue-500 border-2 border-white" />
            <span>Your Location ({userLocation.radius_km} km radius)</span>
          </div>
        )}
        <div className="flex items-center gap-2">
          <div className="w-3 h-3 rounded-full bg-green-500" />
          <span>M &lt; 4</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-3 h-3 rounded-full bg-yellow-500" />
          <span>M 4-5</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-3 h-3 rounded-full" style={{ backgroundColor: '#ea580c' }} />
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
