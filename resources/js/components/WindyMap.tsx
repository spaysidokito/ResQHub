import { useState } from 'react';

export default function WindyMap() {
  const [isLoading, setIsLoading] = useState(true);


  const windyEmbedUrl = 'https://embed.windy.com/embed2.html?lat=14.600&lon=121.000&detailLat=14.600&detailLon=121.000&width=650&height=450&zoom=5&level=surface&overlay=wind&product=ecmwf&menu=&message=true&marker=&calendar=now&pressure=&type=map&location=coordinates&detail=&metricWind=default&metricTemp=default&radarRange=-1';

  return (
    <div className="relative">
      {isLoading && (
        <div className="absolute inset-0 flex items-center justify-center bg-gray-900 rounded-lg z-10">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
            <p className="text-gray-400">Loading weather map...</p>
          </div>
        </div>
      )}

      <iframe
        src={windyEmbedUrl}
        className="w-full rounded-lg border-0"
        style={{ height: '500px', minHeight: '500px' }}
        onLoad={() => setIsLoading(false)}
        title="Windy Weather Map - Typhoon Tracking"
      />


    </div>
  );
}
