export default function Chip ({ title, onClick }) {

    const handleClick = () => {
        if (onClick) {
            onClick();
        }
    };

    return (
        <div onClick={handleClick ?? null} className={`whitespace-pre inline-block w-min bg-accent-500 rounded-md px-2 py-1 text-sm font-md text-secondary mr-2 mb-2 ${onClick ? 'hover:bg-accent cursor-pointer' : null }`}>
            {title}
        </div>
    );
}