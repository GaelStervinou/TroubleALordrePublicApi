import { useEffect, useState } from "react";
import SetUpInstance from "../utils/axios.js";
import { useNavigate } from "react-router-dom";
import Chip from "../components/atoms/Chip.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import { updateUser } from "../queries/users.js";

export default function BackOfficeUsers() {
  const [users, setUsers] = useState([]);
  const [isUpdating, setIsUpdating] = useState(false);

  const http = SetUpInstance();
  const navigate = useNavigate();
  const { isLoggedIn, isAdmin, getMe, retrieveUser } = useAuth();

  useEffect(() => {
    if (!isLoggedIn() && !isAdmin()) {
      navigate('/');
    } 

    getMe();
  }, []);

  useEffect(() => {
    const fetchUsers = async () => {
      const response = await http.get("/users");
      setUsers(response.data["hydra:member"]);
    };
    fetchUsers();
  }, [isUpdating]);

  const openUser = (userId) => {
    navigate(`/profile/${userId}`);
  }

  const roleUpdate = (userId) => {
    navigate(`/admin/users/${userId}/update`);
  }

  const banUser = async (userId) => {
    await updateUser(userId, {status: -2});

    setIsUpdating(true);
  }

  const unbanUser = async (userId) => {
    await updateUser(userId, {status: 0});

    setIsUpdating(true);
  }

  const validateUser = async (userId) => {
    await updateUser(userId, {status: 1});

    setIsUpdating(true);
  }
    

  return (
    <div className={"space-y-4"}>
      {users.length > 0 ? (
        users.map((user) => {
          const roles = user.roles; 

          const getStatus = (status) => {
            switch (status) {
              case 1:
                return 'Actif';
              case -1:
                return 'Supprimé';
              case -2:
                return 'Banni';
              default:
                return 'En attente';
            }
          }

          const isMe = retrieveUser().id === user.id;


          return (
            <article className={'flex flex-col gap-4 py-2'} key={user.id}>
              <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
                  <div className={'flex flex-col gap-1'}>
                      <h5 className={'text-xl font-medium'}>{user.email}</h5>
                      <div className={'flex gap-1'}>
                          { roles.map((role, index) => (
                              <Chip key={index} title={
                                role === 'ROLE_USER' ? 'Client' :
                                role === 'ROLE_COMPANY_ADMIN' ? 'PDG' :
                                role === 'ROLE_ADMIN' ? 'Administrateur' :
                                role === 'ROLE_TROUBLE_MAKER' ? 'Prestataire' : null
                            } />
                          ))}
                      </div>
                      <p className={'text-base'}>{user.firstname} {user.lastname}</p>
                      { user.kbis && (
                        <p className={'text-md font-bold'}>KBIS : {user.kbis}</p>
                      )}
                      <p className={'text-secondary text-base font-bold'}>Statut : {getStatus(user.status)}</p>
                  </div>
                  
                  <div className="flex flex-row gap-2 max-sm:w-full">
                      <Button
                          hasBackground
                          title="Ouvrir"
                          onClick={ () => openUser(user.id) }
                          className={'!bg-primary !text-background max-md:w-full'}/>
                      <Button
                          hasBackground
                          title="Mettre à jour les rôles"
                          onClick={ () => roleUpdate(user.id) }
                          className={'!bg-secondary !text-background max-md:w-full'}/>
                      { !isMe && (
                        <div>
                        { user.status === 0 ? (
                            <Button
                              hasBackground
                              title="Valider l'utilisateur"
                              onClick={ () => validateUser(user.id) }
                              className={'!bg-success !text-background max-md:w-full'}/>
                          ) : (
                            user.status === 1 ? (
                              <Button
                                  hasBackground
                                  title="Bannir"
                                  onClick={ () => banUser(user.id) }
                                  className={'!bg-danger !text-background max-md:w-full'}/>
                            ) : (
                              <Button
                                  hasBackground
                                  title="Débannir"
                                  onClick={ () => unbanUser(user.id) }
                                  className={'!bg-accent !text-background max-md:w-full'}/>
                            )
                          )}
                      </div>
                      )}
                  </div>
              </header>
            </article>
          )
        })
      ) : (
        <div className="flex flex-col gap-5">
          <div className="skeleton w-full h-44"></div>
          <div className="skeleton w-full h-44"></div>
          <div className="skeleton w-full h-44"></div>
        </div>
      )}
    </div>
  );
  
  
}