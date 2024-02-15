import {AiOutlineRight} from "react-icons/ai";

export default function CardLite({ imagePath, title, path }) {

    return (
        <a href={path}>
            <div className="relative cursor-pointer w-full hover:scale-105 rounded-xl shadow-sm flex flex-col gap-3  transition-all duration-700">
                <div className="ambilight hover:ambilight-on mt-2 max-sm:!h-44">
                    <img
                        src={imagePath}
                        alt=""
                        className="light w-full transition-all duration-700 rounded-xl"/>
                    <img
                        src={imagePath}
                        alt="Image"
                        className="w-full transition-all duration-700 z-10 rounded-xl relative object-cover h-full"/>
                </div>
                <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent h-32 z-10 rounded-xl" />
                <div className="absolute bottom-0 left-0 p-4 flex flex-col gap-2 z-20 text-white text-2xl font-medium max-md:text-lg">
                    <strong>{title}</strong>
                    <div className={'text-primary text-base flex items-center gap-2 max-md:text-sm'}>
                        Voir plus
                        <AiOutlineRight />
                    </div>
                </div>
            </div>
        </a>
    );
}