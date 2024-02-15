import CardLite from "../molecules/CardLite.jsx";

export default function CardLiteList({ items }) {

    return (
        <div className="grid grid-cols-4 gap-8 py-10 max-md:px-1 mt-2 max-md:grid-cols-2 max-md:gap-6">
            {items.map((item, index) => (
                <CardLite key={index} id={item.id} imagePath={item.image} title={item.title} path={item.path} />
            ))}
        </div>
    );
}