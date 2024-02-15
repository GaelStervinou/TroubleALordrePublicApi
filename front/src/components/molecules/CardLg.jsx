import {IoIosPin} from "react-icons/io";
import {AiOutlineRight} from "react-icons/ai";
import {Link} from "react-router-dom";
import Chip from "../atoms/Chip.jsx";

export default function CardLg({ imagePath, title, path = null, date = null, address, rate = null , id = null, duration}) {

    date = new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });

    duration = new Date(duration * 1000).toISOString().substring(11, 16)
    return (
        <Link to={path}>
            <div className="cursor-pointer my-8 w-full hover:scale-105 rounded-md shadow-sm flex gap-6 transition-all duration-700">
                <div className="ambilight hover:ambilight-on !h-[200px] w-[280px]">
                    <img
                        src={imagePath}
                        alt=""
                        className="light w-full transition-all duration-700 rounded-md"/>
                    <img
                        src={imagePath}
                        alt="Image"
                        className="w-full transition-all duration-700 z-10 rounded-md relative object-cover h-full"/>
                </div>
                <div className="text-text text-xl max-sm:text-lg hover:text-secondary font-bold w-full flex flex-col justify-between items-end">
                    <header className={'flex flex-col gap-2 w-full'}>
                        {title}
                        <div className={'flex gap-3 text-base font-normal mb-2 items-center max-md:gap-1'}>
                            <IoIosPin />
                            <p>{address}</p>
                        </div>
                        <div className="flex flex-nowrap overflow-x-auto">
                            <Chip title={date} />
                            <Chip title={`durée ${duration}`} />
                        </div>
                    </header>

                    <div className={'text-primary text-base flex items-center gap-2 max-md:text-sm'}>
                        Voir plus
                        <AiOutlineRight />
                    </div>
                </div>
            </div>
        </Link>
    );
}