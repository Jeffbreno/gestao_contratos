(function (doc) {
  "use strict";

  function carregarModal(idDoItem) {
    fetch("/admin/inscritos/visualizar/" + idDoItem)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Erro na solicitação.");
        }
        return response.text(); // Retorna os dados como texto
      })
      .then((data) => {
        data = JSON.parse(data);
        const DadosInscrito = document.getElementById("dados-inscrito");
        const ticket = document.getElementById("ticket");
        ticket.innerHTML = data.id;
        DadosInscrito.innerHTML =
          '\
        <h1>Detalhes do Usuário</h1>\
          <ul>\
              <li><strong>Nome:</strong> <span id="nome">' +
          data.nome.toUpperCase() +
          '</span></li>\
              <li><strong>Email:</strong> <span id="email">' +
          data.email.toLowerCase() +
          '</span></li>\
              <li><strong>Gênero:</strong> <span id="genero">' +
          data.genero +
          '</span></li>\
              <li><strong>CPF:</strong> <span id="cpf">' +
          data.cpf +
          '</span></li>\
              <li><strong>Categoria:</strong> <span id="categoria">' +
          data.titulo +
          '</span></li>\
              <li><strong>Data de Nascimento:</strong> <span id="dt_nascimento">' +
          data.dt_nascimento +
          '</span></li>\
              <li><strong>Celular:</strong> <span id="celular">' +
          data.celular +
          '</span></li>\
              <li><strong>Logradouro:</strong> <span id="logradouro">' +
          data.logradouro.toUpperCase() +
          '</span></li>\
              <li><strong>Número:</strong> <span id="numero">' +
          data.numero +
          '</span></li>\
              <li><strong>Complemento:</strong> <span id="complemento">' +
          data.complemento.toUpperCase() +
          '</span></li>\
              <li><strong>Bairro:</strong> <span id="bairro">' +
          data.bairro.toUpperCase() +
          '</span></li>\
              <li><strong>Cidade:</strong> <span id="cidade">' +
          data.cidade.toUpperCase() +
          '</span></li>\
              <li><strong>UF:</strong> <span id="uf">' +
          data.uf.toUpperCase() +
          '</span></li>\
              <li><strong>Distância:</strong> <span id="distancia">' +
          data.distancia +
          '</span></li>\
              <li><strong>Camisa:</strong> <span id="camisa">' +
          data.camisa +
          '</span></li>\
              <li><strong>Equipe:</strong> <span id="equipe">' +
          data.equipe.toUpperCase() +
          '</span></li>\
              <li><strong>Data de Cadastro:</strong> <span id="dt_cadastro">' +
          data.dt_cadastro +
          '</span></li>\
              <li><strong>Status de Pagamento:</strong> <span id="status_pag">' +
          data.status_pag +
          "</span></li>\
          </ul>";
      })
      .catch((error) => {
        console.error("Erro:", error);
      });
  }

  function excluirItem(idDoItem) {
    console.log(idDoItem);
    // Fazer a requisição AJAX para excluir o item
    // fetch("/admin/testimonies/" + 0 + "/delete", {
    //   method: "POST",
    // })
    //   .then((response) => {
    //     if (response.ok) {
    //       console.log(response);
    //       // Se a exclusão foi bem-sucedida, remover o item da lista na tela
    //       const linhaExcluida = document.querySelector(`tr[data-id="${idDoItem}"]`);
    //       if (linhaExcluida) {
    //         linhaExcluida.remove();
    //       }
    //     } else {
    //       // Tratar o erro ou mostrar uma mensagem de falha
    //       console.error("Falha ao excluir o item.");
    //     }
    //   })
    //   .catch((error) => {
    //     // Tratar erros de requisição, caso ocorram
    //     console.error("Erro na requisição:", error);
    //   });
  }

  // Evento para excluir o item quando um botão de exclusão for clicado
  const botaoVisualizar = doc.querySelectorAll(".btn-visualizar");
  botaoVisualizar.forEach((botao) => {
    botao.addEventListener("click", (event) => {
      event.preventDefault(); // Impede a ação padrão do botão (se houver)
      const idDoItem = botao.getAttribute("data-id");

      carregarModal(idDoItem);
    });
  });

  // Evento para excluir o item quando um botão de exclusão for clicado
  const botoesExclusao = doc.querySelectorAll(".btn-excluir");
  botoesExclusao.forEach((botao) => {
    botao.addEventListener("click", (event) => {
      event.preventDefault(); // Impede a ação padrão do botão (se houver)
      const idDoItem = botao.getAttribute("data-id");

      abrirModalConfirmacao(idDoItem);
    });
  });

  // Função para abrir a modal de confirmação
  let handlerExcluir = null;
  function abrirModalConfirmacao(idDoItem) {
    $("#modal-confirmacao").modal("show");
    let botaoExclusao = doc.getElementById("btn-confirmar-exclusao");
    if (handlerExcluir) {
      botaoExclusao.removeEventListener("click", handlerExcluir);
    }

    handlerExcluir = () => {
      // Chamar a função de exclusão passando o idDoItem
      excluirItem(idDoItem);
      // Fechar a modal após a exclusão
      $("#modal-confirmacao").modal("hide");
    };

    botaoExclusao.addEventListener("click", handlerExcluir);
  }
})(document);
