import React, { useEffect, useState } from "react";
import SetUpInstance from "../utils/axios.js";
import { useNavigate, NavLink, useParams } from "react-router-dom";
import { useAuth } from "../app/authContext.jsx";
import { deleteAvailability } from "../queries/availabilities.js";
import {useTranslator} from "../app/translatorContext.jsx";


export default function AvailabilitiesBackOffice() {
  const [availabilities, setAvailabilities] = useState([]);
  const [isUpdating, setIsUpdating] = useState(false);
  const {translate} = useTranslator();

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
    const fetchAvailabilities = async () => {
      const response = await http.get("/availabilities");
      setAvailabilities(response.data["hydra:member"]);
    };
    fetchAvailabilities();
  }, [isUpdating]);

  const openAvailability = (availabilityId) => {
    navigate(`/admin/availabilities/${availabilityId}/update`);
  }

  const handleDeleteAvailability = async (availabilityId) => {
    await deleteAvailability(availabilityId);

    setIsUpdating(true);
  }

  return (
    <div className={"space-y-4"}>
      <NavLink
        to={`/${companySlug}/admin/gestion/availabilities/create`}
        className={
          "flex justify-center items-center gap-2 bg-primary text-background font-bold p-2 rounded w-full mx-auto max-sm:w-full"
        }
      >
        Ajouter une disponibilité
      </NavLink>
      {availabilities.length > 0 ? (
        availabilities.map((availability) => {
          return (
            <div key={availability.id}>
              <p>{availability.id}</p>
              <p>{availability.day}</p>
              <p>{availability.startTime}</p>
              <p>{availability.endTime}</p>
              <button onClick={ () => openAvailability(availability.id) }>{translate("update")}</button>
              <button onClick={ () => handleDeleteAvailability(availability.id) }>{translate("remove")}</button>
            </div>
          );
        }
        )
      ) : (
        <p>{translate("no-availability")}</p>
      )}
    </div>
  );
}
