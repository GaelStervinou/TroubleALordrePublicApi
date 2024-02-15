export  default function Carousel({pictures}) {
    return (
        <div className="carousel carousel-center w-full space-x-4 bg-transparent rounded-box">
            {pictures?.map((picture, index) => (
                <div key={index} className={`carousel-item`}>
                    <img src={`${import.meta.env.VITE_API_BASE_URL}${picture.contentUrl}`} className="h-96 rounded-box object-cover" alt={'image'}/>
                </div>
            ))}
        </div>
    )
}