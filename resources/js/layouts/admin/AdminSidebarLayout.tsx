import { PropsWithChildren } from 'react';
import { Button } from '@/components/ui/button';
import { usePage, Link } from '@inertiajs/react';

export default function AdminSidebarLayout({ children }: PropsWithChildren) {
    return (
        <div className="flex h-screen bg-black">
            {}
            <aside className="w-64 bg-black p-6 flex flex-col gap-6 min-h-screen border-r-2 border-red-600">
                <Button asChild variant="default" className="w-full bg-black text-white border-2 border-red-600 hover:bg-red-950">
                    <Link href="/admin">Dashboard</Link>
                </Button>
                <Button asChild variant="default" className="w-full bg-black text-white border-2 border-red-600 hover:bg-red-950">
                    <Link href="/admin/accounts">Accounts</Link>
                </Button>
                <Button asChild variant="default" className="w-full bg-black text-white border-2 border-red-600 hover:bg-red-950">
                    <Link href="/admin/teams">Teams</Link>
                </Button>
                <Button asChild variant="default" className="w-full bg-black text-white border-2 border-red-600 hover:bg-red-950">
                    <Link href="/admin/shop">Shop</Link>
                </Button>
            </aside>
            {}
            <main className="flex-1 bg-black p-12 overflow-y-auto text-white min-h-screen">{children}</main>
        </div>
    );
}
