import { useEffect, useState } from "react";
import SetUpInstance from "../utils/axios.js";
import Chip from "../components/atoms/Chip.jsx";
import Button from "../components/atoms/Button.jsx";
import { NavLink, useNavigate, useParams } from "react-router-dom";
import { useAuth } from "../app/authContext.jsx";

export default function UserUnavailabilities() {
  const [unavailabilities, setUnavailabilities] = useState([]);
  const [isUpdating, setIsUpdating] = useState(false);

  const http = SetUpInstance();
  const navigate = useNavigate();
  const { isLoggedIn, isCompanyAdmin } = useAuth();
  const { userId } = useParams();

  useEffect(() => {
    if (!isLoggedIn() && !isCompanyAdmin()) {
      navigate('/');
    } 
  }, []);

  useEffect(() => {
    const fetchUnavailabilities = async () => {
      const response = await http.get(`/users/${userId}/unavailabilities`);
      setUnavailabilities(response.data["hydra:member"]);
    };
    fetchUnavailabilities();
  }, [isUpdating]);

  const deleteUnavailability = async (unavailabilityId) => {
    await http.delete(`/unavailabilities/${unavailabilityId}`);
    setIsUpdating(!isUpdating);
  }


  return (
    <div className={"space-y-4"}>
      <NavLink
        to={`/profile/${userId}/planning/unavailabilities/create`}
        className={
          "flex justify-center items-center gap-2 bg-primary text-background font-bold p-2 rounded w-full mx-auto max-sm:w-full"
        }
      >
        Ajouter une indisponibilité
      </NavLink>
      {unavailabilities.length > 0 ? (
        unavailabilities.map((unavailability) => (
          <article className={'flex flex-col gap-4 py-2'} key={unavailability["@id"]}>
            <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
              <div className="flex flex-row gap-5">
                <div className={'flex flex-row gap-1 mx-auto'}>
                  <div>
                    <Chip title={unavailability.date} />
                  </div>
                  <div className={'flex gap-1'}>
                    {unavailability.shifts.map((shift, index) => (
                      <div key={index} className="flex flex-row gap-3">
                        <p className="text-secondary">
                          de {shift.startTime} à {shift.endTime}
                        </p>
                        <Button
                          hasBackground
                          title={'Supprimer'}
                          onClick={() => deleteUnavailability(shift[0]["@id"])}
                          className={'bg-danger text-background'}
                        />
                      </div>
                    ))}
                  </div>
                </div>
              </div>
              <div className="flex flex-row gap-2 max-sm:w-full">

              </div>
            </header>
          </article>
        ))
      ) : (
        <p>Pas d'indisponibilité trouvé pour cet utilisateur 😢</p>
      )}
    </div>
  );
}