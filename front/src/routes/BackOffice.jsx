import {NavLink, Outlet} from "react-router-dom";

export default function BackOffice() {

    return (
        <div className={'mt-28 max-sm:mt-16 w-full px-16 max-sm:p-8'}>
            <div className={'mb-12 max-sm:mb-8 w-full'}>
                <div role="tablist" className="tabs tabs-boxed bg-surface font-bold mb-12 max-sm:mb-8">
                    <NavLink to={'/admin/establishments'} role="tab" className="tab text-primary">Etablissements</NavLink>
                    <NavLink to={'/admin/users'} role="tab" className="tab text-primary">Utilisateurs</NavLink>
                    <NavLink to={'/admin/categories'} role="tab" className="tab text-primary">Categories</NavLink>
                </div>
            </div>
            <div className={'space-y-2 mb-40'}>
                <Outlet/>
            </div>
        </div>
    )
}