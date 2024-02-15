import {Link} from "react-router-dom";

export default function CardRounded({ imagePath, title, id = false, onClick }) {

    const handleClick = () => {
        if (onClick) {
            onClick();
        }
    };

    return (
        <>
            { !id ?
                (
                    <article onClick={handleClick ?? null} className="flex justify-center items-center flex-col cursor-pointer">
                        <div className="story-outer-circle flex justify-center items-center mb-4 max-sm:mb-3 max-sm:w-24 max-sm:h-24">
                            <img
                                className={'rounded-full h-[93%] w-[93%] object-cover border-surface border-[6px]'}
                                src={imagePath}
                                alt={title}
                            />
                        </div>
                        <div className="font-sm text-sm text-center">{title}</div>
                    </article>
                ) : (
                    <Link to={`/profile/${id}`}>
                        <article className="flex justify-center items-center flex-col cursor-pointer">
                            <div className="story-outer-circle flex justify-center items-center mb-4 max-sm:mb-3 max-sm:w-24 max-sm:h-24">
                                <img
                                    className={'rounded-full h-[93%] w-[93%] object-cover border-surface border-[6px]'}
                                    src={imagePath}
                                    alt={title}
                                />
                            </div>
                            <div className="font-sm text-sm text-center">{title}</div>
                        </article>
                    </Link>
                )
            }
        </>

    );
}