import Comment from "../molecules/Comment.jsx";
import {useRef} from "react";
import {AiFillCaretLeft, AiFillCaretRight} from "react-icons/ai";

export default function CommentList({ items }) {

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
            <div className={'flex flex-col gap-4 absolute top-0 left-0 z-20 h-full py-10 max-sm:hidden'}>
                <button className="h-1/2 backdrop-blur-md bg-surface hover:bg-on-surface transition-all duration-500 rounded-md p-4 max-sm:p-2 shadow-lg" onClick={scrollLeft}>
                    <AiFillCaretLeft className="text-3xl text-primary max-sm:text-2xl" />
                </button>
                <button className="h-1/2 backdrop-blur-md bg-surface hover:bg-on-surface transition-all duration-500 rounded-md p-4 max-sm:p-2 shadow-lg" onClick={scrollRight}>
                    <AiFillCaretRight className="text-3xl text-primary max-sm:text-2xl" />
                </button>
            </div>

            <div className="flex overflow-x-auto scrollbar-hide gap-6 max-sm:gap-3 pl-24 max-sm:pl-0 rounded-r-xl" ref={listRef}>
                {items?.map((item, index) => (
                    <Comment
                        key={index}
                        authorImagePath={`${import.meta.env.VITE_API_BASE_URL}${item?.rated?.profilePicture?.contentUrl ?? '/'}`}
                        content={item.content}
                        date={item.createdAt}
                        rate={item.value}
                        author={`${item.rated.firstname} ${item.rated.lastname}`}
                    />
                ))}
            </div>
        </div>
    );
}