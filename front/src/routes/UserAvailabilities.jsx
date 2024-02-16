import { useEffect, useState } from "react";
import SetUpInstance from "../utils/axios.js";
import Chip from "../components/atoms/Chip.jsx";
import Button from "../components/atoms/Button.jsx";
import { NavLink, useNavigate, useParams } from "react-router-dom";
import { useAuth } from "../app/authContext.jsx";

export default function UserAvailabilities() {
  const [availabilities, setAvailabilities] = useState([]);
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
    const fetchAvailabilities = async () => {
      const response = await http.get(`/users/${userId}/availabilities`);
      setAvailabilities(response.data["hydra:member"]);
    };
    fetchAvailabilities();
  }, [isUpdating]);

  const deleteAvailability = async (availabilityId) => {
    await http.delete(`${availabilityId}`);
    setIsUpdating(!isUpdating);
  }

  return (
    <div className={"space-y-4"}>
      <NavLink
        to={`/profile/${userId}/planning/availabilities/create`}
        className={
          "flex justify-center items-center gap-2 bg-primary text-background font-bold p-2 rounded w-full mx-auto max-sm:w-full"
        }
      >
        Ajouter une disponibilité
      </NavLink>
      {availabilities.length > 0 ? (
        availabilities.map((availability) => (
          <article className={'flex flex-col gap-4 py-2'} key={availability["@id"]}>
            <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
              <div className="flex flex-row gap-5">
                <div className={'flex flex-row gap-1 mx-auto'}>
                  <Chip title={availability.date} />
                  <div className={'flex gap-1'}>
                    {availability.shifts.map((shift, index) => (
                      <p key={`${availability.id}-${index}`} className="text-secondary">
                        de {shift.startTime} à {shift.endTime}
                      </p>
                    ))}
                  </div>
                </div>
              </div>
              <div className="flex flex-row gap-2 max-sm:w-full">
              <Button
                  hasBackground
                  title={'Supprimer'}
                  onClick={() => deleteAvailability(availability.id)}
                  className={'bg-danger text-background'}
                />
              </div>
            </header>
          </article>
        ))
      ) : (
        <p>Pas de disponibilité trouvé pour cet utilisateur 😢</p>
      )}
    </div>
  );
}