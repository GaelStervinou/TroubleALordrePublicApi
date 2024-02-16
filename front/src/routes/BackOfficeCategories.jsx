import {NavLink, useParams, useNavigate} from "react-router-dom";
import { useEffect, useState } from "react";
import ListItem from "../components/molecules/ListItem.jsx";
import SetUpInstance from "../utils/axios.js";
import { useAuth } from "../app/authContext.jsx";
import { getCategories } from "../queries/categories.js";

export default function BackOfficeCategories() {
  const [categories, setCategories] = useState([]);
  const [isUpdating, setIsUpdating] = useState(false);

  const http = SetUpInstance();
  const navigate = useNavigate();
  const { isLoggedIn, isAdmin } = useAuth();

  useEffect(() => {
    if (!isLoggedIn() && !isAdmin()) {
      navigate('/');
    } 
  }, []);

  useEffect(() => {
    const fetchCategories = async () => {
      const response = await getCategories();
      setCategories(response);
    };
    fetchCategories();
  }, [isUpdating]);

  const updateCategory = (categoryId) => {
    navigate(`/admin/categories/${categoryId}/update`);
  }

  const deleteCategory = async (categoryId) => {
    await http.delete(`/categories/${categoryId}`);
    setIsUpdating(!isUpdating);
  }

  const createCategory = () => {
    navigate(`/admin/categories/create`);
  }

  return (
    <div className={"space-y-4"}>
      <NavLink
        onClick={createCategory}
        className={
          "flex justify-center items-center gap-2 bg-primary text-background font-bold p-2 rounded w-full mx-auto max-sm:w-full"
        }
      >
        Ajouter une catégorie
      </NavLink>
      {categories.length > 0 ? (
        categories.map((category) => {
          return (
            <ListItem
              key={category["@id"]}
              title={category.name}
              updateAction={ () => updateCategory(category.id) }
              deleteAction={ () => deleteCategory(category.id) }
            />
          );
        })
      ) : (
        <div className="flex flex-col gap-5">
          <div className="skeleton w-full h-24"></div>
          <div className="skeleton w-full h-24"></div>
          <div className="skeleton w-full h-24"></div>
        </div>
      )}
    </div>
  );
}
