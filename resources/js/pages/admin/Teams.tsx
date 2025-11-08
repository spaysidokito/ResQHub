import AdminSidebarLayout from '@/layouts/admin/AdminSidebarLayout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Eye } from 'lucide-react';
import { useState } from 'react';

const mockTeams = [
    { id: 1, name: 'Alpha Team', members: 5 },
    { id: 2, name: 'Beta Team', members: 3 },
];

export default function AdminTeams() {
    const [teams] = useState(mockTeams);

    return (
        <AdminSidebarLayout>
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-2xl font-bold text-white">Teams</h1>
                <Button variant="default" className="bg-black text-white border border-neutral-800 hover:bg-neutral-900">create team</Button>
            </div>
            <Card className="p-6 rounded-xl border border-neutral-800 bg-black max-w-4xl mx-auto">
                <table className="w-full text-left border-collapse bg-black text-white">
                    <thead>
                        <tr className="border-b border-neutral-800">
                            <th className="py-2 px-4">ID</th>
                            <th className="py-2 px-4">Name</th>
                            <th className="py-2 px-4">Members</th>
                            <th className="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {teams.map((team) => (
                            <tr key={team.id} className="border-b border-neutral-800 last:border-b-0 hover:bg-neutral-900">
                                <td className="py-2 px-4">{team.id}</td>
                                <td className="py-2 px-4">{team.name}</td>
                                <td className="py-2 px-4">{team.members}</td>
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
