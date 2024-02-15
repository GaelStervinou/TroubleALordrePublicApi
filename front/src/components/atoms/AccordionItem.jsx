export default function AccordionItem({ title, children, isCheck = false, accordionId}) {

    return (
        <div className="collapse collapse-arrow bg-surface" >
            <input
                type="radio"
                name={accordionId}
            />
            <div className="collapse-title text-base text-primary font-medium">
                {title}
            </div>
            <div className="collapse-content">
                {children}
            </div>
        </div>
    );
}