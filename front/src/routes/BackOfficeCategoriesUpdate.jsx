import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import { updateCategory, getCategory } from "../queries/categories.js";

export default function BackOfficeCategoriesUpdate() {
  const [name, setName] = useState("");
  const [areMissingInfos, setAreMissingInfos] = useState(false);
  const [error, setError] = useState(false);

  const { isAdmin, isLoggedIn } = useAuth();
  const navigate = useNavigate();
  const { categoryId } = useParams();

  useEffect(() => {
    if (!isLoggedIn() || !isAdmin()) {
      navigate('/');
    } 
  }, []);

  useEffect(() => {
    const fetchCategory = async () => {
      const response = await getCategory(categoryId);
      setName(response.name);
    };
    fetchCategory();
  }, []);

  const handleNameChange = (event) => {
    setName(event.target.value);
  }

  const handleSubmit = async () => {
    if (!name || name.length < 5) {
      setAreMissingInfos(true);
      return;
    }

    try {
      await updateCategory(categoryId, name);
      navigate('/admin/categories');
    } catch (error) {
      setError(true);
    }
    
  }
  
  return (
    <div className="mt-32 bg-background max-sm:mt-28 w-1/2">
      <h1 className="text-4xl text-color-effect font-heading text-center">Mettre à jour une catégorie</h1>
      <div className="hero-content flex-col w-full lg:flex-row-reverse">
          <div className="card shrink-0 max-w-sm w-full shadow-2xl bg-surface">
              <div className="card-body">
                  <TextInput
                      placeholder="Nom"
                      type="text"
                      value={name}
                      handleValueChange={handleNameChange}
                  />
                  {areMissingInfos && <WarningAlert message="Veuillez renseigner un nom de catégorie de plus de 5 caractères" />}
                  {error && <WarningAlert message="Une erreur est survenue" />}
                  <Button
                      hasBackground
                      title="Update la catégorie"
                      onClick={handleSubmit}
                      className={'!w-full !bg-primary text-background hover:!bg-secondary mt-5'}/>
              </div>
          </div>
      </div>
    </div>
  );
}