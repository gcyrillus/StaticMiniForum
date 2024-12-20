        window.addEventListener("load", (event) => {
            document.body.insertAdjacentHTML( 'afterbegin', myformtpl);
            const MyForm =document.querySelector('#staticMiniForum');
            let template = document.getElementById("myform");
            let templateContent = template.content;
            MyForm.appendChild(templateContent);
        });