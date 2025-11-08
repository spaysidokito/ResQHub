interface Earthquake {
  id: number;
  magnitude: number;
  location: string;
  depth: number;
  occurred_at: string;
  updated_at?: string;
}

export default function EarthquakeList({ earthquakes }: { earthquakes: Earthquake[] }) {
  const getMagnitudeClass = (magnitude: number) => {
    if (magnitude >= 7) return 'text-red-600 font-bold';
    if (magnitude >= 6) return 'text-orange-600 font-bold';
    if (magnitude >= 5) return 'text-yellow-600 font-bold';
    return 'text-green-500';
  };

  const getTimeAgo = (date: string) => {
    const now = new Date();
    const then = new Date(date);
    const diff = Math.floor((now.getTime() - then.getTime()) / 1000);

    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return `${Math.floor(diff / 86400)}d ago`;
  };

  return (
    <div className="space-y-2 max-h-96 overflow-y-auto">
      {earthquakes.length === 0 ? (
        <div className="text-center text-gray-500 py-8">
          No earthquake data available
        </div>
      ) : (
        earthquakes.map((eq) => (
          <div
            key={eq.id}
            className="bg-gray-800 border border-gray-700 hover:border-red-600 rounded-lg p-4 transition cursor-pointer"
          >
            <div className="flex items-start justify-between">
              <div className="flex-1">
                <div className="flex items-center space-x-3">
                  <span className={`text-2xl font-bold ${getMagnitudeClass(eq.magnitude)}`}>
                    M{eq.magnitude.toFixed(1)}
                  </span>
                  <div>
                    <h4 className="font-semibold text-white">{eq.location}</h4>
                    <p className="text-sm text-gray-400">
                      Depth: {eq.depth}km • Reported {getTimeAgo(eq.updated_at || eq.occurred_at)}
                    </p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        ))
      )}
    </div>
  );
}
