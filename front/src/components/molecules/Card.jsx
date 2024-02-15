import Rating from "../atoms/Rating.jsx";
import ChipList from "./ChipList.jsx";

export default function Card({ imagePath, title, path, categories, rate }) {

    return (
        <a href={path}>
            <div className="cursor-pointer w-60 hover:scale-105 min-w-60 max-sm:w-40 max-sm:min-w-40 rounded-md shadow-sm flex flex-col gap-3 mx-4 transition-all duration-700">
                <div className="ambilight hover:ambilight-on mt-[65px] max-sm:!h-40">
                    <img
                        src={imagePath}
                        alt=""
                        className="light w-full transition-all duration-700 rounded-md"/>
                    <img
                        src={imagePath}
                        alt="Image"
                        className="w-full transition-all duration-700 z-10 rounded-md relative object-cover h-full"/>
                </div>
                <div className="text-text text-xl max-sm:text-lg hover:text-secondary font-bold flex flex-col gap-3 w-full">
                    {title}
                    <ChipList chips={categories}/>
                    <Rating value={rate}/>
                </div>
            </div>
        </a>
    );
}