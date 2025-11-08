import ResQHubAuthLayout from '@/layouts/auth/resqhub-auth-layout';

export default function AuthLayout({ children, title, description, ...props }: { children: React.ReactNode; title: string; description: string }) {
    return (
        <ResQHubAuthLayout title={title} description={description} {...props}>
            {children}
        </ResQHubAuthLayout>
    );
}
