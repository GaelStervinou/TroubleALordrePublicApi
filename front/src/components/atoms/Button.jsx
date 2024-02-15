export default function Button ({ title, icon, onClick, hasBackground, href, className, type, disabled = false }) {
    const buttonStyle = hasBackground
        ? 'bg-white hover:bg-gray-400 text-black'
        : 'bg-transparent text-white';

    const handleClick = () => {
        if (onClick) {
            onClick();
        }
    };

    return href ? (
        <a
            href={href}
            {...(disabled ? { disabled: true } : {})}
            className={`py-2 px-4 rounded-lg w-min ${buttonStyle} font-medium flex items-center transition-all cursor-pointer justify-center duration-500 ${className ?? ''}`}
        >
            {icon && <span className="mr-2">{icon}</span>}
            <span className="whitespace-nowrap">{title}</span>
        </a>
    ) : (
        <button
            onClick={handleClick ?? null}
            type={type ?? 'button'}
            {...(disabled ? { disabled: true } : {})}
            className={`py-2 px-4 rounded-lg w-min ${buttonStyle} flex items-center transition-all cursor-pointer justify-center duration-500 ${className ?? ''}`}
        >
            {icon && <span className="mr-2">{icon}</span>}
            <span className="whitespace-nowrap">{title}</span>
        </button>
    );
}