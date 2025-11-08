import { useState, useEffect } from 'react';
import { offlineStorage } from '@/services/offlineStorage';
import PublicIcon from '@mui/icons-material/Public';
import WavesIcon from '@mui/icons-material/Waves';
import TyphoonIcon from '@mui/icons-material/Cyclone';
import LocalFireDepartmentIcon from '@mui/icons-material/LocalFireDepartment';
import InfoIcon from '@mui/icons-material/Info';

export default function OfflineSafetyTips() {
  const [safetyTips, setSafetyTips] = useState<any[]>([]);
  const [selectedType, setSelectedType] = useState<string>('all');

  useEffect(() => {
    // Initialize safety tips in storage
    offlineStorage.saveSafetyTips();

    // Load tips
    loadTips();
  }, [selectedType]);

  const loadTips = () => {
    if (selectedType === 'all') {
      setSafetyTips(offlineStorage.getSafetyTips());
    } else {
      setSafetyTips(offlineStorage.getSafetyTips(selectedType));
    }
  };

  const getIcon = (type: string) => {
    switch (type) {
      case 'earthquake':
        return <PublicIcon sx={{ fontSize: 20 }} className="text-orange-400" />;
      case 'typhoon':
        return <TyphoonIcon sx={{ fontSize: 20 }} className="text-blue-400" />;
      case 'flood':
        return <WavesIcon sx={{ fontSize: 20 }} className="text-cyan-400" />;
      case 'fire':
        return <LocalFireDepartmentIcon sx={{ fontSize: 20 }} className="text-red-400" />;
      default:
        return <InfoIcon sx={{ fontSize: 20 }} className="text-gray-400" />;
    }
  };

  const types = [
    { key: 'all', label: 'All', icon: InfoIcon },
    { key: 'earthquake', label: 'Earthquake', icon: PublicIcon },
    { key: 'typhoon', label: 'Typhoon', icon: TyphoonIcon },
    { key: 'flood', label: 'Flood', icon: WavesIcon },
    { key: 'fire', label: 'Fire', icon: LocalFireDepartmentIcon },
  ];

  return (
    <div className="bg-gray-900 rounded-lg border-2 border-red-600 p-4 md:p-6">
      <h3 className="text-lg md:text-xl font-bold text-red-500 mb-4">
        📚 Offline Safety Guide
      </h3>

      {/* Type Filter */}
      <div className="flex flex-wrap gap-2 mb-4">
        {types.map((type) => (
          <button
            key={type.key}
            onClick={() => setSelectedType(type.key)}
            className={`px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 transition ${
              selectedType === type.key
                ? 'bg-red-600 text-white'
                : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
            }`}
          >
            <type.icon sx={{ fontSize: 16 }} />
            {type.label}
          </button>
        ))}
      </div>

      {/* Safety Tips List */}
      <div className="space-y-3 max-h-96 overflow-y-auto">
        {safetyTips.length > 0 ? (
          safetyTips.map((tip) => (
            <div
              key={tip.id}
              className="bg-gray-800 rounded-lg p-3 border border-gray-700"
            >
              <div className="flex items-start gap-2 mb-2">
                {getIcon(tip.type)}
                <h4 className="text-white font-semibold text-sm flex-1">
                  {tip.title}
                </h4>
              </div>
              <p className="text-gray-300 text-xs leading-relaxed">
                {tip.content}
              </p>
              <span className="inline-block mt-2 px-2 py-0.5 bg-gray-700 text-gray-400 text-xs rounded capitalize">
                {tip.type}
              </span>
            </div>
          ))
        ) : (
          <div className="text-center text-gray-500 py-8">
            <InfoIcon sx={{ fontSize: 48 }} className="mb-2" />
            <p>No safety tips available</p>
          </div>
        )}
      </div>

      {/* Emergency Contacts */}
      <div className="mt-4 pt-4 border-t border-gray-800">
        <h4 className="text-white font-semibold text-sm mb-2">
          📞 Emergency Contacts (Philippines)
        </h4>
        <div className="grid grid-cols-2 gap-2 text-xs">
          <div className="bg-gray-800 p-2 rounded">
            <p className="text-gray-400">NDRRMC</p>
            <p className="text-white font-bold">911</p>
          </div>
          <div className="bg-gray-800 p-2 rounded">
            <p className="text-gray-400">Red Cross</p>
            <p className="text-white font-bold">143</p>
          </div>
          <div className="bg-gray-800 p-2 rounded">
            <p className="text-gray-400">PAGASA</p>
            <p className="text-white font-bold">(02) 8927-1335</p>
          </div>
          <div className="bg-gray-800 p-2 rounded">
            <p className="text-gray-400">BFP (Fire)</p>
            <p className="text-white font-bold">(02) 8426-0219</p>
          </div>
        </div>
      </div>

      <p className="text-gray-500 text-xs mt-4 text-center">
        ℹ️ Available offline - No internet required
      </p>
    </div>
  );
}
