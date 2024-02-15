import {NavLink, Outlet, useParams, useNavigate} from "react-router-dom";
import { useEffect, useState } from "react";
import { getCompanyInvitations, updateInvitation } from "../queries/invitations.js";
import Button from "../components/atoms/Button.jsx";
import Chip from "../components/atoms/Chip.jsx";

export default function InvitationsBackOffice() {
  const [invitations, setInvitations] = useState([]);
  const [isUpdating, setIsUpdating] = useState(false);

  const { companySlug } = useParams();

  useEffect(() => {
    const fetchInvitations = async () => {
      const fetchedInvitations = await getCompanyInvitations(companySlug);
      setInvitations(fetchedInvitations["hydra:member"]);
      setIsUpdating(false);
    };
    fetchInvitations();
  }, [companySlug, isUpdating]);

  const deleteInvitationAction = (invitationId) => {
    updateInvitation(invitationId, { status: "canceled" });
    setIsUpdating(true);
  }

  return (
    <div className={"space-y-4"}>
      <NavLink
        to={`/${companySlug}/admin/invitations/create`}
        className={
          "flex justify-center items-center gap-2 bg-primary text-background font-bold p-2 rounded w-full mx-auto max-sm:w-full"
        }
      >
        Ajouter une invitation
      </NavLink>
      {invitations.length > 0 ? (
        invitations.map((invitation) => {
          const information = invitation.receiver;
          const date = new Date(invitation.createdAt);
          return (
            <article className={'flex flex-col gap-4 py-2'} key={invitation.id}>
              <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
                  <div className={'flex flex-col gap-1'}>
                    <div className="flex flex-row gap-5">
                      <h5 className={'text-xl font-medium'}>{information.email}</h5>
                      <Chip title={invitation.status} />
                    </div>
                      <Chip title={`Créée le ${date.toLocaleDateString()} à ${date.toLocaleTimeString()}`} />
                  </div>
                  <div className="flex flex-row gap-2 max-sm:w-full">
                    { invitation.status === "pending" && (
                      <Button
                          hasBackground
                          title="Supprimer"
                          onClick={ () => deleteInvitationAction(invitation['@id']) }
                          className={'!bg-danger !text-background max-md:w-full'}/>
                    )}
                  </div>
              </header>
              <div className={'flex flex-col gap-2'}>
                  <p className={'text-base'}>
                      {information.firstname} {information.lastname}
                  </p>
              </div>
              <hr/>
          </article>
          );
        })
      ) : (
        <p>Aucune invitation pour le moment</p>
      )}
    </div>
  );

}
