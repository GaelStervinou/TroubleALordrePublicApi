import Rating from "../atoms/Rating.jsx";
import ChipList from "./ChipList.jsx";
import {IoIosPin} from "react-icons/io";
import {AiOutlineRight} from "react-icons/ai";
import {Link} from "react-router-dom";
import {FaChartLine} from "react-icons/fa";

export default function CardRow({ imagePath, title, path = null, categories = null, address, rate = null , id = null}) {

    return (
        <>
            { path ? (
                <Link to={path}>
                    <div className="cursor-pointer w-full hover:scale-105 rounded-md shadow-sm flex gap-6 transition-all duration-700">
                        <div className="ambilight hover:ambilight-on h-[200px] w-[200px] max-sm:!h-[140px] max-sm:w-[140px]">
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
                                    <IoIosPin className={'max-sm:hidden'}/>
                                    <p>{address}</p>
                                </div>
                                {categories ?
                                    (<ChipList chips={categories}/>) :
                                    null
                                }
                                {rate ?
                                    (<Rating value={rate}/>) :
                                    null
                                }
                            </header>

                            <div className={'text-primary text-base flex items-center gap-2 max-md:text-sm'}>
                                Voir plus
                                <AiOutlineRight />
                            </div>
                        </div>
                    </div>
                </Link>
            ) :
            (
                <div className="cursor-pointer w-full hover:scale-105 rounded-md shadow-sm flex gap-6 transition-all duration-700">
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
                    <div className="text-text text-2xl max-sm:text-lg hover:text-secondary font-bold w-full flex flex-col justify-between items-end">
                        <header className={'flex flex-col gap-2 w-full'}>
                            {title}
                            <div className={'flex gap-3 text-base font-normal mb-2 items-center max-md:gap-1'}>
                                <IoIosPin />
                                <p>{address}</p>
                            </div>
                            {categories ??
                                <ChipList chips={categories}/>
                            }
                        </header>
                        <div className={'flex gap-6 justify-center'}>
                            <Link to={`/${id}/dashboard/`}>
                                <div className={'text-primary text-base flex items-center gap-2 max-md:text-sm'}>
                                    Dashboard
                                    <FaChartLine />
                                </div>
                            </Link>
                            <Link to={`/${id}/admin/gestion`}>
                                <div className={'text-primary text-base flex items-center gap-2 max-md:text-sm'}>
                                    Back office
                                    <AiOutlineRight />
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            )}
        </>

    );
}