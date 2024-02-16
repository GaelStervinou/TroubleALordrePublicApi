import React, { useEffect, useState } from "react";
import SetUpInstance from "../utils/axios.js";
import { useNavigate, NavLink, useParams} from "react-router-dom";
import { useAuth } from "../app/authContext.jsx";
import { deleteUnavailability } from "../queries/unavailabilities.js";

export default function UnavailabilitiesBackOffice() {
  const [unavailabilities, setUnavailabilities] = useState([]);
  const [isUpdating, setIsUpdating] = useState(false);

  const http = SetUpInstance();
  const navigate = useNavigate();
  const { companySlug } = useParams();
  const { isLoggedIn, isAdmin, getMe } = useAuth();

  useEffect(() => {
    if (!isLoggedIn() && !isAdmin()) {
      navigate('/');
    } 

    getMe();
  }, []);

  useEffect(() => {
    const fetchUnavailabilities = async () => {
      const response = await http.get("/unavailabilities");
      setUnavailabilities(response.data["hydra:member"]);
    };
    fetchUnavailabilities();
  }, [isUpdating]);

  const openUnavailability = (unavailabilityId) => {
    navigate(`/admin/unavailabilities/${unavailabilityId}/update`);
  }

  const handleDeleteUnavailability = async (unavailabilityId) => {
    await deleteUnavailability(unavailabilityId);

    setIsUpdating(true);
  }

  return (
    <div className={"space-y-4"}>
      <NavLink
        to={`${companySlug}/admin/gestion/unavailabilities/create`}
        className={
          "flex justify-center items-center gap-2 bg-primary text-background font-bold p-2 rounded w-full mx-auto max-sm:w-full"
        }
      >
        Ajouter une indisponibilité
      </NavLink>
      {unavailabilities.length > 0 ? (
        unavailabilities.map((unavailability) => {
          return (
            <div key={unavailability.id}>
              <p>{unavailability.id}</p>
              <p>{unavailability.startDate}</p>
              <p>{unavailability.endDate}</p>
              <button onClick={ () => openUnavailability(unavailability.id) }>Modifier</button>
              <button onClick={ () => handleDeleteUnavailability(unavailability.id) }>Supprimer</button>
            </div>
          );
        }
        )
      ) : (
        <p>Aucune indisponibilité pour le moment</p>
      )}
    </div>
  );
}
