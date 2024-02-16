import CardRounded from "../molecules/CardRounded.jsx";

export default function CardRoundedList({ items }) {

    return (
        <div className="overflow-x-scroll flex gap-8 w-full max-w-full scrollbar-hide px-8 max-sm:px-6 max-sm:gap-6">
            {items?.map((item, index) => (
                <CardRounded key={index} id={item.id} imagePath={`${import.meta.env.VITE_API_BASE_URL}${item.profilePicture.contentUrl ?? '/'}`} title={`${item.firstname} ${item.lastname}`} />
            ))}
        </div>
    );
}