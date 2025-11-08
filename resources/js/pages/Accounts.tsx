import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Eye } from 'lucide-react';
import { useState } from 'react';

const mockAccounts = [
    { id: 12, name: 'John Doe', team: 'camelCase' },

];

export default function Accounts() {
    const [accounts] = useState(mockAccounts);

    return (
        <div className="flex h-screen">
            {}
            <aside className="w-64 bg-neutral-200 p-6 flex flex-col gap-6">
                <Button variant="outline" className="w-full">Dashboard</Button>
                <Button variant="outline" className="w-full">Accounts</Button>
                <Button variant="outline" className="w-full">Teams</Button>
                <Button variant="outline" className="w-full">Shop</Button>
            </aside>
            {}
            <main className="flex-1 bg-white p-12">
                <div className="flex justify-between items-center mb-8">
                    <h1 className="text-2xl font-bold">Accounts</h1>
                    <Button variant="outline">create account</Button>
                </div>
                <Card className="p-6 rounded-xl border max-w-4xl mx-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="border-b">
                                <th className="py-2 px-4">ID</th>
                                <th className="py-2 px-4">Name</th>
                                <th className="py-2 px-4">Team</th>
                                <th className="py-2 px-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {accounts.map((account) => (
                                <tr key={account.id} className="border-b last:border-b-0 hover:bg-neutral-50">
                                    <td className="py-2 px-4">{account.id}</td>
                                    <td className="py-2 px-4">{account.name}</td>
                                    <td className="py-2 px-4">{account.team}</td>
                                    <td className="py-2 px-4 text-center">
                                        <Eye className="inline h-5 w-5 text-yellow-500" />
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </Card>
            </main>
        </div>
    );
}
