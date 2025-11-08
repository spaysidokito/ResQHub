import { Head, Link, useForm } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Users, Plus, UserCheck, Crown } from 'lucide-react';

interface TeamsIndexProps {
    ownedTeams: any[];
    memberTeams: any[];
}

export default function TeamsIndex({ ownedTeams, memberTeams }: TeamsIndexProps) {
    const { data, setData, post, processing, errors } = useForm({
        invite_code: '',
    });

    const handleJoinTeam = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('teams.join'));
    };

    return (
        <>
            <Head title="Teams" />

            <div className="container mx-auto p-6 space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Teams</h1>
                        <p className="text-muted-foreground">Manage your teams and join new ones</p>
                    </div>
                    <Button asChild>
                        <Link href={route('teams.create')}>
                            <Plus className="h-4 w-4 mr-2" />
                            Create Team
                        </Link>
                    </Button>
                </div>

                {/* Join Team Form */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <UserCheck className="h-5 w-5" />
                            Join a Team
                        </CardTitle>
                        <CardDescription>
                            Enter an invite code to join an existing team
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleJoinTeam} className="flex gap-4">
                            <div className="flex-1">
                                <Label htmlFor="invite_code">Invite Code</Label>
                                <Input
                                    id="invite_code"
                                    type="text"
                                    value={data.invite_code}
                                    onChange={(e) => setData('invite_code', e.target.value)}
                                    placeholder="Enter invite code..."
                                    className={errors.invite_code ? 'border-red-500' : ''}
                                />
                                {errors.invite_code && (
                                    <p className="text-sm text-red-500 mt-1">{errors.invite_code}</p>
                                )}
                            </div>
                            <div className="flex items-end">
                                <Button type="submit" disabled={processing}>
                                    Join Team
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                {/* Owned Teams */}
                {ownedTeams.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Crown className="h-5 w-5" />
                                Teams You Own
                            </CardTitle>
                            <CardDescription>
                                Teams where you are the owner
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {ownedTeams.map((team) => (
                                <div key={team.id} className="flex items-center justify-between p-4 border rounded-lg">
                                    <div>
                                        <h3 className="font-semibold">{team.name}</h3>
                                        <p className="text-sm text-muted-foreground">{team.description}</p>
                                        <div className="flex items-center gap-2 mt-2">
                                            <Badge variant="secondary">
                                                {team.members?.length || 0} members
                                            </Badge>
                                            <Badge variant="outline">Owner</Badge>
                                            <Badge variant="outline" className="font-mono text-xs">
                                                {team.invite_code}
                                            </Badge>
                                        </div>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button asChild size="sm">
                                            <Link href={route('teams.show', team.id)}>View</Link>
                                        </Button>
                                        <Button asChild size="sm" variant="outline">
                                            <Link href={route('teams.activities.index', team.id)}>Activities</Link>
                                        </Button>
                                    </div>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                )}

                {/* Member Teams */}
                {memberTeams.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Users className="h-5 w-5" />
                                Teams You're In
                            </CardTitle>
                            <CardDescription>
                                Teams where you are a member
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {memberTeams.map((team) => (
                                <div key={team.id} className="flex items-center justify-between p-4 border rounded-lg">
                                    <div>
                                        <h3 className="font-semibold">{team.name}</h3>
                                        <p className="text-sm text-muted-foreground">{team.description}</p>
                                        <div className="flex items-center gap-2 mt-2">
                                            <Badge variant="secondary">
                                                {team.members?.length || 0} members
                                            </Badge>
                                            <Badge variant="outline">Member</Badge>
                                            <p className="text-sm text-muted-foreground">
                                                Owner: {team.owner?.name}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button asChild size="sm">
                                            <Link href={route('teams.show', team.id)}>View</Link>
                                        </Button>
                                        <Button asChild size="sm" variant="outline">
                                            <Link href={route('teams.activities.index', team.id)}>Activities</Link>
                                        </Button>
                                    </div>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                )}

                {/* Empty State */}
                {ownedTeams.length === 0 && memberTeams.length === 0 && (
                    <Card>
                        <CardContent className="text-center py-12">
                            <Users className="h-16 w-16 text-muted-foreground mx-auto mb-4" />
                            <h3 className="text-lg font-semibold mb-2">No Teams Yet</h3>
                            <p className="text-muted-foreground mb-4">
                                You're not part of any teams yet. Create your own team or join an existing one to get started.
                            </p>
                            <div className="flex gap-4 justify-center">
                                <Button asChild>
                                    <Link href={route('teams.create')}>
                                        <Plus className="h-4 w-4 mr-2" />
                                        Create Team
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}
