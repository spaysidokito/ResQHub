import AdminSidebarLayout from '@/layouts/admin/AdminSidebarLayout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Eye, Pencil, Trash2 } from 'lucide-react';
import { useForm, usePage, router } from '@inertiajs/react';
import { useState } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';

interface Account {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface AccountsPageProps {
    accounts: {
        data: Account[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: any[];
    };
}

const roles = ['admin', 'manager', 'user'];

export default function AdminAccounts() {
    const { accounts, errors, flash } = usePage<AccountsPageProps & { errors: any; flash: any }>().props;
    const [open, setOpen] = useState(false);
    const [editOpen, setEditOpen] = useState(false);
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [selected, setSelected] = useState<Account | null>(null);
    const form = useForm({
        name: '',
        email: '',
        password: '',
        role: 'user',
    });
    const editForm = useForm({
        name: '',
        email: '',
        role: 'user',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('accounts.store'), {
            onSuccess: () => {
                setOpen(false);
                form.reset();
            },
        });
    };

    const handleEdit = (account: Account) => {
        setSelected(account);
        editForm.setData({ name: account.name, email: account.email, role: account.role });
        setEditOpen(true);
    };

    const handleEditSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!selected) return;
        editForm.patch(route('accounts.update', selected.id), {
            onSuccess: () => {
                setEditOpen(false);
                setSelected(null);
            },
        });
    };

    const handleDelete = (account: Account) => {
        setSelected(account);
        setDeleteOpen(true);
    };

    const confirmDelete = () => {
        if (!selected) return;
        router.delete(route('accounts.destroy', selected.id), {
            onSuccess: () => {
                setDeleteOpen(false);
                setSelected(null);
            },
        });
    };

    return (
        <AdminSidebarLayout>
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-2xl font-bold text-red-500">Accounts</h1>
                <Button variant="default" className="bg-black text-white border-2 border-red-600 hover:bg-red-950" onClick={() => setOpen(true)}>
                    create account
                </Button>
            </div>
            {flash.success && <div className="mb-4 text-green-400">{flash.success}</div>}
            <Card className="p-6 rounded-xl border-2 border-red-600 bg-black max-w-6xl mx-auto">
                <table className="w-full text-left border-collapse bg-black text-white">
                    <thead>
                        <tr className="border-b-2 border-red-600">
                            <th className="py-2 px-4">ID</th>
                            <th className="py-2 px-4">Name</th>
                            <th className="py-2 px-4">Email</th>
                            <th className="py-2 px-4">Role</th>
                            <th className="py-2 px-4">Title</th>
                            <th className="py-2 px-4">Title Description</th>
                            <th className="py-2 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {accounts.data.map((account) => (
                            <tr key={account.id} className="border-b border-red-600 last:border-b-0 hover:bg-red-950">
                                <td className="py-2 px-4">{account.id}</td>
                                <td className="py-2 px-4">{account.name}</td>
                                <td className="py-2 px-4">{account.email}</td>
                                <td className="py-2 px-4">{account.role}</td>
                                <td className="py-2 px-4">{account.title || '-'}</td>
                                <td className="py-2 px-4">{account.title_description || '-'}</td>
                                <td className="py-2 px-4 text-center flex gap-2 justify-center">
                                    <Button size="icon" variant="ghost" className="text-yellow-400" onClick={() => handleEdit(account)}><Pencil className="h-4 w-4" /></Button>
                                    <Button size="icon" variant="ghost" className="text-red-400" onClick={() => handleDelete(account)}><Trash2 className="h-4 w-4" /></Button>
                                    <Eye className="inline h-5 w-5 text-yellow-400 ml-2" />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {}
                <div className="flex justify-end mt-4 gap-2">
                    {accounts.links.map((link, i) => (
                        <Button
                            key={i}
                            variant="ghost"
                            className={`px-3 py-1 text-xs ${link.active ? 'bg-neutral-800 text-white' : 'text-neutral-400'}`}
                            disabled={!link.url}
                            asChild
                        >
                            <a href={link.url || '#'} dangerouslySetInnerHTML={{ __html: link.label }} />
                        </Button>
                    ))}
                </div>
            </Card>
            {}
            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="bg-black border-2 border-red-600 text-white">
                    <DialogHeader>
                        <DialogTitle>Create Account</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block mb-1">Name</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={form.data.name}
                                onChange={e => form.setData('name', e.target.value)}
                                required
                            />
                            {errors.name && <div className="text-red-400 text-xs mt-1">{errors.name}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Email</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={form.data.email}
                                onChange={e => form.setData('email', e.target.value)}
                                required
                                type="email"
                            />
                            {errors.email && <div className="text-red-400 text-xs mt-1">{errors.email}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Password</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={form.data.password}
                                onChange={e => form.setData('password', e.target.value)}
                                required
                                type="password"
                            />
                            {errors.password && <div className="text-red-400 text-xs mt-1">{errors.password}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Role</label>
                            <select
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={form.data.role}
                                onChange={e => form.setData('role', e.target.value)}
                                required
                            >
                                {roles.map(role => (
                                    <option key={role} value={role}>{role}</option>
                                ))}
                            </select>
                            {errors.role && <div className="text-red-400 text-xs mt-1">{errors.role}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Title</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={form.data.title || ''}
                                onChange={e => form.setData('title', e.target.value)}
                                type="text"
                            />
                            {errors.title && <div className="text-red-400 text-xs mt-1">{errors.title}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Title Description</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={form.data.title_description || ''}
                                onChange={e => form.setData('title_description', e.target.value)}
                                type="text"
                            />
                            {errors.title_description && <div className="text-red-400 text-xs mt-1">{errors.title_description}</div>}
                        </div>
                        <DialogFooter>
                            <Button type="submit" variant="default" className="bg-black text-white border-2 border-red-600 hover:bg-red-950" disabled={form.processing}>
                                {form.processing ? 'Creating...' : 'Create'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
            {}
            <Dialog open={editOpen} onOpenChange={setEditOpen}>
                <DialogContent className="bg-black border-2 border-red-600 text-white">
                    <DialogHeader>
                        <DialogTitle>Edit Account</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleEditSubmit} className="space-y-4">
                        <div>
                            <label className="block mb-1">Name</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={editForm.data.name}
                                onChange={e => editForm.setData('name', e.target.value)}
                                required
                            />
                            {errors.name && <div className="text-red-400 text-xs mt-1">{errors.name}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Email</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={editForm.data.email}
                                onChange={e => editForm.setData('email', e.target.value)}
                                required
                                type="email"
                            />
                            {errors.email && <div className="text-red-400 text-xs mt-1">{errors.email}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Role</label>
                            <select
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={editForm.data.role}
                                onChange={e => editForm.setData('role', e.target.value)}
                                required
                            >
                                {roles.map(role => (
                                    <option key={role} value={role}>{role}</option>
                                ))}
                            </select>
                            {errors.role && <div className="text-red-400 text-xs mt-1">{errors.role}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Title</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={editForm.data.title || ''}
                                onChange={e => editForm.setData('title', e.target.value)}
                                type="text"
                            />
                            {errors.title && <div className="text-red-400 text-xs mt-1">{errors.title}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Title Description</label>
                            <input
                                className="w-full px-3 py-2 rounded bg-neutral-900 border-2 border-red-600 text-white"
                                value={editForm.data.title_description || ''}
                                onChange={e => editForm.setData('title_description', e.target.value)}
                                type="text"
                            />
                            {errors.title_description && <div className="text-red-400 text-xs mt-1">{errors.title_description}</div>}
                        </div>
                        <DialogFooter>
                            <Button type="submit" variant="default" className="bg-black text-white border-2 border-red-600 hover:bg-red-950" disabled={editForm.processing}>
                                {editForm.processing ? 'Saving...' : 'Save'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
            {}
            <Dialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                <DialogContent className="bg-black border-2 border-red-600 text-white">
                    <DialogHeader>
                        <DialogTitle>Delete Account</DialogTitle>
                    </DialogHeader>
                    <div className="mb-4">Are you sure you want to delete <span className="font-bold">{selected?.name}</span>?</div>
                    <DialogFooter>
                        <Button variant="destructive" className="bg-red-700 text-white border border-red-900 hover:bg-red-800" onClick={confirmDelete} disabled={editForm.processing}>
                            Delete
                        </Button>
                        <Button variant="ghost" className="ml-2" onClick={() => setDeleteOpen(false)}>
                            Cancel
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AdminSidebarLayout>
    );
}
