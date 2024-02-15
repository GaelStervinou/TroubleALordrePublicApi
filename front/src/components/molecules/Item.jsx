import Button from "../atoms/Button.jsx";
import Chip from "../atoms/Chip.jsx";

export default function Item({ title, path, description, duration, price}) {

    duration = new Date(duration * 1000).toISOString().substring(11, 16)


    return (
        <article className={'flex flex-col gap-4 py-2'}>
            <header className={'flex justify-between items-start max-md:flex-col gap-2 max-md:gap-4'}>
                <div className={'flex flex-col gap-1'}>
                    <h5 className={'text-xl font-medium'}>{title}</h5>
                    <div className={'flex gap-1'}>
                        <Chip title={`${duration}`} />
                        <p className={'text-secondary text-base font-bold'}>{price}€</p>
                    </div>
                </div>
                <Button
                    hasBackground
                    title="Prendre rendez-vous"
                    href={path}
                    className={'!bg-primary !text-background max-md:w-full'}/>
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