export default function Rating({value = null, isDisabled = false}) {

    return (
        <>
            { value ?
                (<div className="rating">
                    <div className={`${value > 0 ? 'bg-secondary':'bg-accent-500'} mask w-3.5 h-3.5 rounded-full transition-all duration-500 mr-1`}></div>
                    <div className={`${value > 1.5 ? 'bg-secondary':'bg-accent-500'} mask w-3.5 h-3.5 rounded-full transition-all duration-500 mr-1`}></div>
                    <div className={`${value > 2.5 ? 'bg-secondary':'bg-accent-500'} mask w-3.5 h-3.5 rounded-full transition-all duration-500 mr-1`}></div>
                    <div className={`${value > 3.5 ? 'bg-secondary':'bg-accent-500'} mask w-3.5 h-3.5 rounded-full transition-all duration-500 mr-1`}></div>
                    <div className={`${value > 4.5 ? 'bg-secondary':'bg-accent-500'} mask w-3.5 h-3.5 rounded-full transition-all duration-500 mr-1`}></div>
                </div> ) :
                (<div className="rating">
                    <input
                        type="radio"
                        value={1}
                        name="rating-4"
                        className="mask w-3.5 h-3.5 rounded-full bg-secondary transition-all duration-500 mr-1"
                    />
                    <input
                        type="radio"
                        name="rating-4"
                        value={2}
                        className="mask w-3.5 h-3.5 rounded-full bg-secondary transition-all duration-500 mr-1"
                    />
                    <input
                        type="radio"
                        name="rating-4"
                        value={3}
                        className="mask w-3.5 h-3.5 rounded-full bg-secondary transition-all duration-500 mr-1"
                    />
                    <input
                        type="radio"
                        name="rating-4"
                        value={4}
                        className="mask w-3.5 h-3.5 rounded-full bg-secondary transition-all duration-500 mr-1"
                    />
                    <input
                        type="radio"
                        name="rating-4"
                        value={5}
                        className="mask w-3.5 h-3.5 rounded-full bg-secondary transition-all duration-500 mr-1"
                    />
                </div>)
            }
        </>
    )
}