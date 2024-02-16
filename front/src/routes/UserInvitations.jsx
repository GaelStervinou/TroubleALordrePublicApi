import {NavLink, useParams} from "react-router-dom";
import { useEffect, useState } from "react";
import { getMyInvitations, updateInvitation } from "../queries/invitations.js";
import Button from "../components/atoms/Button.jsx";
import Chip from "../components/atoms/Chip.jsx";
import {useTranslator} from "../app/translatorContext.jsx";

export default function UserInvitations() {
  const [invitations, setInvitations] = useState([]);
  const [isUpdating, setIsUpdating] = useState(false);

  useEffect(() => {
    const fetchInvitations = async () => {
      const fetchedInvitations = await getMyInvitations();
      setInvitations(fetchedInvitations["hydra:member"]);
      setIsUpdating(false);
    };
    fetchInvitations();
  }, [isUpdating]);
  const {translate} = useTranslator();

  const refuseInvitationAction = (invitationId) => {
    updateInvitation(invitationId, { status: "refused" });
    setIsUpdating(true);
  }

  const acceptInvitationAction = (invitationId) => {
    updateInvitation(invitationId, { status: "accepted" });
    setIsUpdating(true);
  }

  return (
    <div className={"space-y-4"}>

      {invitations.length > 0 ? (
        invitations.map((invitation) => {
          const date = new Date(invitation.createdAt);
          return (
            <article className={'flex flex-col gap-4 py-2'} key={invitation.id}>
              <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
                  <div className={'flex flex-row gap-1'}>
                  <img src={`${import.meta.env.VITE_API_BASE_URL}${invitation.company.mainMedia.contentUrl}`} alt={invitation.company.name} className={'w-28 h-28 object-fit'}/>
                    <div className="flex flex-col gap-2">
                      <h5 className={'text-xl font-medium'}>{invitation.company.name}</h5>
                      <Chip title={`Créée le ${date.toLocaleDateString()} à ${date.toLocaleTimeString()}`} />
                      <Chip title={invitation.status} />
                    </div>
                  </div>
                    { invitation.status === "pending" && (
                      <div className="flex flex-row gap-2 max-sm:w-full">
                        <Button
                            hasBackground
                            title={translate("accept")}
                            onClick={ () => acceptInvitationAction(invitation['@id']) }
                            className={'!bg-success !text-background max-md:w-full'}/>
                        <Button
                            hasBackground
                            title={translate("refuse")}
                            onClick={ () => refuseInvitationAction(invitation['@id']) }
                            className={'!bg-danger !text-background max-md:w-full'}/>
                      </div>
                    )}
              </header>
              <hr/>
          </article>
          );
        })
      ) : (
        <p>{translate("no-invitation")}</p>
      )}
    </div>
  );

}
