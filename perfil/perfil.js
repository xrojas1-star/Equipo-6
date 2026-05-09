const uploadPhoto = document.getElementById("uploadPhoto");

const profileImage = document.getElementById("profileImage");

const miniProfileImage = document.getElementById("miniProfileImage");

const removePhoto = document.getElementById("removePhoto");

/* IMAGEN DEFAULT */
const defaultImage = "imagenes/default.png";

/* CARGAR FOTO GUARDADA */

const savedImage = localStorage.getItem("profileImage");

if(savedImage){

    profileImage.src = savedImage;
    miniProfileImage.src = savedImage;

}

/* CAMBIAR FOTO */

uploadPhoto.addEventListener("change", function(){

    const file = this.files[0];

    if(file){

        const reader = new FileReader();

        reader.onload = function(e){

            const imageUrl = e.target.result;

            profileImage.src = imageUrl;

            miniProfileImage.src = imageUrl;

            localStorage.setItem(
                "profileImage",
                imageUrl
            );

        };

        reader.readAsDataURL(file);
    }

});

/* ELIMINAR FOTO */

removePhoto.addEventListener("click", () => {

    profileImage.src = defaultImage;

    miniProfileImage.src = defaultImage;

    localStorage.removeItem("profileImage");

});

const imageModal =
document.getElementById("imageModal");

const modalImage =
document.getElementById("modalImage");

const closeModal =
document.getElementById("closeModal");

/* ABRIR MODAL */

profileImage.addEventListener("click", () => {

    imageModal.classList.add("active");

    modalImage.src = profileImage.src;

});

/* CERRAR MODAL */

closeModal.addEventListener("click", () => {

    imageModal.classList.remove("active");

});

/* Para cerrar dando click en la X */

imageModal.addEventListener("click", (e) => {

    if(e.target === imageModal){

        imageModal.classList.remove("active");

    }

});