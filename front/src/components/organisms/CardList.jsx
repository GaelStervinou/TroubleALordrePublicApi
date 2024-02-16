import {useRef} from "react";
import {AiFillCaretLeft, AiFillCaretRight} from "react-icons/ai";
import Card from "../molecules/Card.jsx";

export default function CardList({ items }) {
    const listRef = useRef(null);

    const scrollLeft = () => {
        if (listRef.current) {
            listRef.current.scrollBy({
                left: -500,
                behavior: 'smooth',
            });
        }
    };

    const scrollRight = () => {
        if (listRef.current) {
            listRef.current.scrollBy({
                left: 500,
                behavior: 'smooth',
            });
        }
    };



    return (
        <div className="w-full py-10 relative">
            <div className={'flex flex-col gap-4 absolute top-0 left-0 z-20 h-full py-10 pl-16 max-md:pl-8 max-sm:hidden'}>
                <button className="h-1/2 backdrop-blur-md bg-surface hover:bg-on-surface duration-500 transition-all rounded-md p-4 max-sm:p-2 shadow-lg" onClick={scrollLeft}>
                    <AiFillCaretLeft className="text-3xl text-primary max-sm:text-2xl" />
                </button>
                <button className="h-1/2 backdrop-blur-md bg-surface hover:bg-on-surface duration-500 transition-all rounded-md p-4 max-sm:p-2 shadow-lg" onClick={scrollRight}>
                    <AiFillCaretRight className="text-3xl text-primary max-sm:text-2xl" />
                </button>
            </div>
            <div className="flex overflow-x-auto scrollbar-hide px-2 max-sm:px-3 pl-36 max-md:pl-28 mt-[-65px]" ref={listRef}>
                {items.map((item, index) => (
                    <Card key={index} categories={item.categories} id={item.id} imagePath={`${import.meta.env.VITE_API_BASE_URL}${item.mainMedia.contentUrl ?? '/'}`} title={item.name} path={`/${item.id}`} rate={item.averageServicesRatesFromCustomer} />
                ))}
            </div>
        </div>
    );
}