import {NavLink, Outlet, useParams} from "react-router-dom";
import { useAuth } from "../../app/authContext.jsx";


export default function SubMenu({path}) {
    const { userId } = useParams();
    const { companySlug } = useParams();
    const { isTroubleMaker } = useAuth();

    return (
        <>
            <div className={'flex justify-end items-center mb-12 max-sm:mb-8'}>
                <div role="tablist" className="tabs tabs-boxed bg-background">
                    { userId ? (
                        path.map((item, index) => (
                            item.troubleMakerOnly ? (
                                isTroubleMaker() ? (
                                    <NavLink key={index} to={item.path.replace(':userId', userId)} role="tab" className="tab tab-sm text-secondary">{item.title}</NavLink>
                                ) : (
                                    null
                                )
                            ) : (
                                <NavLink key={index} to={item.path.replace(':userId', userId)} role="tab" className="tab tab-sm text-secondary">{item.title}</NavLink>
                            )
                        ))
                    ) : (
                        path.map((item, index) => (
                            <NavLink key={index} to={item.path.replace(':companySlug', companySlug)} role="tab" className="tab tab-sm text-secondary">{item.title}</NavLink>
                        ))
                    )}
                </div>
            </div>
            <div className={'space-y-2 mb-40'}>
                <Outlet/>
            </div>
        </>
    )
}