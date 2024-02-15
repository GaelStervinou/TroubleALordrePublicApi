import Button from "../atoms/Button.jsx";
import Chip from "../atoms/Chip.jsx";

export default function ListItem({ title, description, duration, price, updateAction, deleteAction}) {
 
    return (
      <article className={'flex flex-col gap-4 py-2'}>
        <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
            <div className={'flex flex-col gap-1'}>
                <h5 className={'text-xl font-medium'}>{title}</h5>
                <div className={'flex gap-1'}>
                    { duration && (
                        <Chip title={`${duration} minutes`} />
                    )}
                    { price && (
                        <p className={'text-secondary text-base font-bold'}>{price}€</p>
                    )}
                </div>
            </div>
            <div className="flex flex-row gap-2 max-sm:w-full">
              { updateAction && (
                  <Button
                      hasBackground
                      title="Mettre à jour"
                      onClick={updateAction}
                      className={'!bg-primary !text-background max-md:w-full'}/>
              )}  
              { deleteAction && (
                  <Button
                      hasBackground
                      title="Supprimer"
                      onClick={deleteAction}
                      className={'!bg-danger !text-background max-md:w-full'}/>
              )}
            </div>
        </header>
        <div className={'flex flex-col gap-2'}>
            <p className={'text-base'}>
                {description}
            </p>
        </div>
        <hr/>
    </article>
    );
}