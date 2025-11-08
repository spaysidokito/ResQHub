interface Disaster {
  id: number;
  type: 'flood' | 'typhoon' | 'fire' | 'earthquake';
  name: string;
  description: string;
  location: string;
  severity: 'low' | 'moderate' | 'high' | 'critical';
  status: 'active' | 'monitoring' | 'resolved';
  started_at: string;
  source: string;
  wind_speed?: number;
  wind_direction?: string;
  movement_direction?: string;
  movement_speed?: number;
  pressure?: number;
  last_updated?: string;
}

export default function DisasterPanel({ disasters }: { disasters: Disaster[] }) {
  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'flood': return '🌊';
      case 'typhoon': return '🌀';
      case 'fire': return '🔥';
      case 'earthquake': return '🌍';
      default: return '⚠️';
    }
  };

  const getSeverityClass = (severity: string) => {
    switch (severity) {
      case 'critical': return 'text-red-600 font-bold border-red-600';
      case 'high': return 'text-orange-600 font-bold border-orange-600';
      case 'moderate': return 'text-yellow-600 font-bold border-yellow-600';
      case 'low': return 'text-green-500 border-green-500';
      default: return 'text-gray-500 border-gray-500';
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'active': return 'bg-red-600 text-white';
      case 'monitoring': return 'bg-yellow-600 text-white';
      case 'resolved': return 'bg-green-600 text-white';
      default: return 'bg-gray-600 text-white';
    }
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
      {disasters.length === 0 ? (
        <div className="text-center text-gray-500 py-8">
          No active disasters of this type
        </div>
      ) : (
        disasters.map((disaster) => (
          <div
            key={disaster.id}
            className={`bg-gray-800 border-2 hover:border-red-600 rounded-lg p-3 md:p-4 transition cursor-pointer ${getSeverityClass(disaster.severity)}`}
          >
            <div className="flex items-start justify-between gap-3">
              <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2 mb-2 flex-wrap">
                  <span className="text-2xl">{getTypeIcon(disaster.type)}</span>
                  <h4 className="font-semibold text-white text-sm md:text-base break-words">
                    {disaster.name}
                  </h4>
                  <span className={`px-2 py-1 text-xs rounded uppercase ${getStatusBadge(disaster.status)}`}>
                    {disaster.status}
                  </span>
                </div>

                <p className="text-xs md:text-sm text-gray-300 mb-2 line-clamp-2">
                  {disaster.description}
                </p>

                <div className="flex flex-wrap gap-2 text-xs text-gray-400">
                  <span>📍 {disaster.location}</span>
                  <span>•</span>
                  <span>⏰ {getTimeAgo(disaster.last_updated || disaster.started_at)}</span>
                  <span>•</span>
                  <span>📡 {disaster.source}</span>
                </div>
              </div>

              <div className={`px-2 py-1 rounded text-xs font-bold uppercase whitespace-nowrap ${getSeverityClass(disaster.severity)}`}>
                {disaster.severity}
              </div>
            </div>
          </div>
        ))
      )}
    </div>
  );
}
