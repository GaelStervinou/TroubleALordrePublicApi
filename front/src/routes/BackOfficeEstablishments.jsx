import { useEffect, useState } from "react";
import SetUpInstance from "../utils/axios.js";
import { useNavigate } from "react-router-dom";
import Chip from "../components/atoms/Chip.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import Rating from "../components/atoms/Rating.jsx";

export default function BackOfficeEstablishments() {
  const [establishments, setEstablishments] = useState([]);

  const http = SetUpInstance();
  const navigate = useNavigate();
  const { isLoggedIn, isAdmin } = useAuth();

  useEffect(() => {
    if (!isLoggedIn() && !isAdmin()) {
      navigate('/');
    } 
  }, []);

  useEffect(() => {
    const fetchEstablishments = async () => {
      const response = await http.get("/companies");
      setEstablishments(response.data["hydra:member"]);
    };
    fetchEstablishments();
  }, []);

  const openEstablishment = (establishmentId) => {
    navigate(`/${establishmentId}`);
  }

  return (
    <div className={"space-y-4"}>
      {establishments.length > 0 ? (
        establishments.map((establishment) => {
          var rate = 0;
          if (establishment.averageServicesRatesFromCustomer) {
            rate = establishment.averageServicesRatesFromCustomer.toFixed(2);
          }

          return (
            <article className={'flex flex-col gap-4 py-2'} key={establishment.id}>
              <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
                  <div className="flex flex-row gap-5">
                      <img src={`${import.meta.env.VITE_API_BASE_URL}${establishment.mainMedia.contentUrl ?? '/'}`} alt={establishment.name} className={'w-28 h-28 object-fit'}/>
                      <div className={'flex flex-col gap-1 mx-auto'}>
                          <h5 className={'text-xl font-medium'}>{establishment.name}</h5>
                          <div className={'flex gap-1'}>
                              { establishment.categories.map((category, index) => (
                                <Chip key={index} title={category.name} />
                                ))}
                          </div>
                          <Rating value={rate} isDisabled />
                      </div>
                  </div>
                  <div className="flex flex-row gap-2 max-sm:w-full">
                      <Button
                          hasBackground
                          title="Ouvrir"
                          onClick={ () => openEstablishment(establishment.id) }
                          className={'!bg-primary !text-background max-md:w-full'}/>
                  </div>
              </header>
              <hr/>
          </article>
          );
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