import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ShoppingCart, Coins, Crown, Palette, Zap, Plus } from 'lucide-react';

interface ShopItem {
    id: number;
    name: string;
    description: string;
    points_cost: number;
    type: string;
    available_for: string;
}

interface Team {
    id: number;
    name: string;
    user_points: number;
}

interface Props {
    teams: Team[];
    shopItems: ShopItem[];
}

export default function ShopIndex({ teams, shopItems }: Props) {
    const [selectedTeam, setSelectedTeam] = useState<number | ''>('');
    const { post, processing } = useForm();

    const handlePurchase = (itemId: number) => {
        if (!selectedTeam) {
            alert('Please select a team first');
            return;
        }

        post(route('shop.purchase'), {
            data: {
                item_id: itemId,
                team_id: selectedTeam,
            },
        });
    };

    const getItemIcon = (type: string) => {
        switch (type) {
            case 'badge':
                return <Crown className="h-6 w-6" />;
            case 'theme':
                return <Palette className="h-6 w-6" />;
            case 'multiplier':
                return <Zap className="h-6 w-6" />;
            case 'boost':
                return <Plus className="h-6 w-6" />;
            default:
                return <ShoppingCart className="h-6 w-6" />;
        }
    };

    const getItemColor = (type: string) => {
        switch (type) {
            case 'badge':
                return 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/20';
            case 'theme':
                return 'text-purple-600 bg-purple-100 dark:text-purple-400 dark:bg-purple-900/20';
            case 'multiplier':
                return 'text-orange-600 bg-orange-100 dark:text-orange-400 dark:bg-orange-900/20';
            case 'boost':
                return 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/20';
            default:
                return 'text-blue-600 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/20';
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">Shop</h1>
                    <p className="text-muted-foreground">
                        Spend your earned points on team enhancements and bonuses
                    </p>
                </div>
                <div className="flex items-center gap-2">
                    <Coins className="h-5 w-5 text-yellow-600" />
                    <span className="text-sm font-medium">Points Shop</span>
                </div>
            </div>

            {/* Team Selection */}
            <Card>
                <CardHeader>
                    <CardTitle>Select Team</CardTitle>
                    <CardDescription>
                        Choose which team to spend points from
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Select value={selectedTeam.toString()} onValueChange={(value) => setSelectedTeam(Number(value))}>
                        <SelectTrigger className="w-full max-w-sm">
                            <SelectValue placeholder="Select a team" />
                        </SelectTrigger>
                        <SelectContent>
                            {teams.map((team) => (
                                <SelectItem key={team.id} value={team.id.toString()}>
                                    <div className="flex items-center justify-between w-full">
                                        <span>{team.name}</span>
                                        <Badge variant="secondary" className="ml-2">
                                            {team.user_points} pts
                                        </Badge>
                                    </div>
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </CardContent>
            </Card>

            {/* Shop Items */}
            <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {shopItems.map((item) => (
                    <Card key={item.id} className="relative">
                        <CardHeader>
                            <div className="flex items-center gap-3">
                                <div className={`p-2 rounded-lg ${getItemColor(item.type)}`}>
                                    {getItemIcon(item.type)}
                                </div>
                                <div className="flex-1">
                                    <CardTitle className="text-lg">{item.name}</CardTitle>
                                    <Badge variant="outline" className="mt-1">
                                        {item.points_cost} points
                                    </Badge>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <CardDescription className="mb-4">
                                {item.description}
                            </CardDescription>

                            {item.available_for !== 'all' && (
                                <Badge variant="secondary" className="mb-4">
                                    {item.available_for === 'team_owner' ? 'Team Owner Only' : 'Limited Access'}
                                </Badge>
                            )}

                            <Button
                                onClick={() => handlePurchase(item.id)}
                                disabled={processing || !selectedTeam}
                                className="w-full"
                                variant="default"
                            >
                                {processing ? 'Processing...' : 'Purchase'}
                            </Button>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Points Information */}
            <Card>
                <CardHeader>
                    <CardTitle>How Points Work</CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <h4 className="font-semibold mb-2">Earning Points</h4>
                            <ul className="text-sm text-muted-foreground space-y-1">
                                <li>• Complete tasks and activities</li>
                                <li>• Participate in team challenges</li>
                                <li>• Earn badges and achievements</li>
                                <li>• Help team members</li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold mb-2">Spending Points</h4>
                            <ul className="text-sm text-muted-foreground space-y-1">
                                <li>• Purchase team enhancements</li>
                                <li>• Buy custom badges</li>
                                <li>• Get temporary boosts</li>
                                <li>• Unlock special features</li>
                            </ul>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
