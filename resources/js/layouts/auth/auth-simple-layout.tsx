import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="festival-auth-bg flex min-h-svh flex-col items-center justify-center p-6 md:p-10">
            <div className="w-full max-w-sm">
                <div className="mb-6 flex flex-col items-center gap-2 text-center">
                    <p className="text-[11px] font-extrabold tracking-[0.35em] text-[#F5A623] uppercase">
                        9º Festival de Gastronomia e Cultura
                    </p>
                    <p className="text-3xl font-extrabold tracking-tight text-white">
                        Aracati 2026
                    </p>
                    <p className="text-xs font-semibold text-white/70">
                        Área do organizador e dos expositores
                    </p>
                </div>

                <div className="festival-auth-card rounded-2xl p-6 shadow-2xl md:p-7">
                    {(title || description) && (
                        <div className="mb-6 space-y-1.5 text-center">
                            {title && (
                                <h1 className="text-xl font-extrabold">
                                    {title}
                                </h1>
                            )}
                            {description && (
                                <p className="text-sm text-muted-foreground">
                                    {description}
                                </p>
                            )}
                        </div>
                    )}
                    <div className="flex flex-col gap-6">{children}</div>
                </div>
            </div>
        </div>
    );
}
