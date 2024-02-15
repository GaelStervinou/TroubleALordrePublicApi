import {NavLink, Outlet, useParams} from "react-router-dom";

export default function CompanyBackOffice() {
    const { companySlug } = useParams();

    return (
        <div className={'mt-28 max-sm:mt-16 w-full px-16 max-sm:p-8'}>
            <div className={'mb-12 max-sm:mb-8 w-full'}>
                <div role="tablist" className="tabs tabs-boxed bg-surface font-bold mb-12 max-sm:mb-8">
                    <NavLink to={`/${companySlug}/admin/gestion/services`} role="tab" className="tab text-primary">Gestion</NavLink>
                    <NavLink to={`/${companySlug}/admin/invitations`} role="tab" className="tab text-primary">Invitation</NavLink>
                </div>
            </div>
            <div className={'space-y-2 mb-40'}>
                <Outlet/>
            </div>
        </div>
    )
}