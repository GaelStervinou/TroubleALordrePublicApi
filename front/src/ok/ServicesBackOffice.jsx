import {NavLink, useParams, useNavigate} from "react-router-dom";
import { useEffect, useState } from "react";
import { getServices } from "../queries/services.js";
import ListItem from "../components/molecules/ListItem.jsx";

export default function ServicesBackOffice() {
  const [services, setServices] = useState([]);

  const { companySlug } = useParams();
  const navigate = useNavigate();

  useEffect(() => {
    const fetchServices = async () => {
      const fetchedServices = await getServices(companySlug);
      setServices(fetchedServices["hydra:member"]);
    };
    fetchServices();
  }, [companySlug]);

  const openUpdateService = (serviceId) => {
    navigate(`/${companySlug}/admin/gestion${serviceId}/update`);
  }

  return (
    <div className={"space-y-4"}>
      <NavLink
        to={`/${companySlug}/admin/gestion/services/create`}
        className={
          "flex justify-center items-center gap-2 bg-primary text-background font-bold p-2 rounded w-full mx-auto max-sm:w-full"
        }
      >
        Ajouter un service
      </NavLink>
      {services.length > 0 ? (
        services.map((service) => {
          return (
            <ListItem
              key={service["@id"]}
              title={service.name}
              description={service.description}
              duration={service.duration / 60}
              price={service.price}
              updateAction={ () => openUpdateService(service['@id']) }
            />
          );
        })
      ) : (
        <p>Aucun service pour le moment</p>
      )}
    </div>
  );
}
