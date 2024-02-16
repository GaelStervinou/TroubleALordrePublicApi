import { useEffect, useState } from "react";
import SetUpInstance from "../utils/axios.js";
import Chip from "../components/atoms/Chip.jsx";
import Button from "../components/atoms/Button.jsx";
import { NavLink, useNavigate, useParams } from "react-router-dom";
import { useAuth } from "../app/authContext.jsx";

export default function UserAvailabilities() {
  const [availabilities, setAvailabilities] = useState([]);

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
  }, []);

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
        availabilities.map((availability) => {
          return (
            <article className={'flex flex-col gap-4 py-2'} key={availability.id}>
              <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
                  <div className="flex flex-row gap-5">
                      <div className={'flex flex-col gap-1 mx-auto'}>
                          <div className={'flex gap-1'}>
                              { availability.shifts.map((shift, index) => (
                                <Chip key={index} title={shift.startTime} /> - <Chip key={index} title={shift.endTime} />
                                ))}
                          </div>
                      </div>
                  </div>
                  <div className="flex flex-row gap-2 max-sm:w-full">
                      <Button
                          hasBackground
                          title={'Edit'}
                          onClick={() => navigate(`availabilities/${availability.id}`)}
                      />
                  </div>
              </header>
            </article>
          );
        })
      ) : (
        <p>Pas de disponibilité trouvé pour cet utilisateur 😢</p>
      )}
    </div>
  );


}