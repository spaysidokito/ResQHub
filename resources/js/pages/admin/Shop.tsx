import AdminSidebarLayout from '@/layouts/admin/AdminSidebarLayout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Eye } from 'lucide-react';
import { useState } from 'react';

const mockShopItems = [
    { id: 1, name: 'Custom Badge', type: 'badge' },
    { id: 2, name: 'Team Theme', type: 'theme' },
];

export default function AdminShop() {
    const [items] = useState(mockShopItems);

    return (
        <AdminSidebarLayout>
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-2xl font-bold text-red-500">Shop</h1>
                <Button variant="default" className="bg-black text-white border-2 border-red-600 hover:bg-red-950">create item</Button>
            </div>
            <Card className="p-6 rounded-xl border-2 border-red-600 bg-black max-w-4xl mx-auto">
                <table className="w-full text-left border-collapse bg-black text-white">
                    <thead>
                        <tr className="border-b-2 border-red-600">
                            <th className="py-2 px-4">ID</th>
                            <th className="py-2 px-4">Name</th>
                            <th className="py-2 px-4">Type</th>
                            <th className="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {items.map((item) => (
                            <tr key={item.id} className="border-b border-red-600 last:border-b-0 hover:bg-red-950">
                                <td className="py-2 px-4">{item.id}</td>
                                <td className="py-2 px-4">{item.name}</td>
                                <td className="py-2 px-4">{item.type}</td>
                                <td className="py-2 px-4 text-center">
                                    <Eye className="inline h-5 w-5 text-yellow-400" />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </Card>
        </AdminSidebarLayout>
    );
}
