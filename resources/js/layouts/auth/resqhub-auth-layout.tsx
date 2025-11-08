import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
}

export default function ResQHubAuthLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    return (
        <div className="min-h-screen bg-gray-800 flex items-center justify-center p-4">

            <div className="relative w-full max-w-md">
                {/* Card */}
                <div className="bg-gray-900 rounded-2xl shadow-2xl border-2 border-red-600 overflow-hidden">
                    {/* Header with Logo */}
                    <div className="bg-red-600 p-8 text-center relative">
                        {/* Logo */}
                        <Link href={route('home')} className="relative inline-block">
                            <div className="flex justify-center">
                                {/* ResQHub Logo with white background for visibility */}
                                <div className="bg-white px-6 py-4 rounded-xl inline-block">
                                    <img
                                        src="/images/resqhub-logo.png"
                                        alt="ResQHub Logo"
                                        className="h-24 w-auto object-contain"
                                        onError={(e) => {
                                            // Fallback to text if image not found
                                            e.currentTarget.style.display = 'none';
                                            const fallback = e.currentTarget.parentElement?.nextElementSibling;
                                            if (fallback) fallback.classList.remove('hidden');
                                        }}
                                    />
                                </div>
                                {/* Fallback Text (hidden by default) */}
                                <div className="hidden bg-white px-6 py-4 rounded-xl">
                                    <h1 className="text-3xl font-bold text-red-600">ResQHub</h1>
                                    <p className="text-gray-700 text-sm mt-1">Disaster Monitoring System</p>
                                </div>
                            </div>
                        </Link>
                    </div>

                    {/* Content */}
                    <div className="p-8">
                        <div className="mb-6 text-center">
                            <h2 className="text-2xl font-bold text-white mb-2">{title}</h2>
                            <p className="text-gray-400 text-sm">{description}</p>
                        </div>

                        {children}
                    </div>

                    {/* Footer */}
                    <div className="bg-gray-950 px-8 py-4 border-t border-gray-800">
                        <p className="text-center text-xs text-gray-500">
                            🇵🇭 Keeping Filipinos Safe During Disasters
                        </p>
                    </div>
                </div>

                {/* Emergency Contacts Quick Access */}
                <div className="mt-6 text-center">
                    <p className="text-gray-400 text-xs mb-2">Emergency Hotlines:</p>
                    <div className="flex justify-center gap-4 text-xs">
                        <span className="text-red-400">NDRRMC: 911</span>
                        <span className="text-red-400">Red Cross: 143</span>
                    </div>
                </div>
            </div>
        </div>
    );
}
