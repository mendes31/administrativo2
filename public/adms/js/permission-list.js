// JavaScript para a página de permissões

// Verificar se já foi inicializado para evitar execução dupla
if (window.permissionListInitialized) {
    console.log('JavaScript de permissões já foi inicializado, pulando...');
} else {
    // Marcar como inicializado ANTES de qualquer outra operação
    window.permissionListInitialized = true;
    console.log('JavaScript de permissões carregado! - Timestamp:', new Date().toISOString());
    console.log('Script ID:', Math.random().toString(36).substr(2, 9));
    
    // Função de inicialização para evitar execução dupla
    function initializePermissionList() {
        // Verificar se o DOM já está carregado
        if (document.readyState === 'loading') {
            // DOM ainda carregando, aguardar
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM carregado via listener, inicializando grupos...');
                // Pequeno delay para garantir que o DOM esteja completamente carregado
                setTimeout(function() {
                    initializeGroups();
                    initializeMobileGroups();
                }, 100);
            });
        } else {
            // DOM já carregado, executar imediatamente
            console.log('DOM já estava carregado, inicializando grupos imediatamente...');
            setTimeout(function() {
                initializeGroups();
                initializeMobileGroups();
            }, 100);
        }
    }

    // Chamar a função de inicialização
    initializePermissionList();
}

function initializeGroups() {
    console.log('Inicializando grupos...');
    
    const contentRows = document.querySelectorAll('.group-content-row');
    console.log('Linhas de conteúdo encontradas:', contentRows.length);
    
    const tableHeaders = document.querySelectorAll('.group-table-header');
    console.log('Cabeçalhos de tabela encontrados:', tableHeaders.length);
    
    const headers = document.querySelectorAll('.group-header');
    console.log('Cabeçalhos de grupo encontrados:', headers.length);
    
    // IMPORTANTE: Ocultar TUDO ANTES de qualquer manipulação visual
    // Isso evita o "flash" de conteúdo expandido
    contentRows.forEach(row => {
        row.style.display = 'none';
        row.style.visibility = 'hidden'; // Adicionar para evitar reflow
    });
    
    tableHeaders.forEach(header => {
        header.style.display = 'none';
        header.style.visibility = 'hidden'; // Adicionar para evitar reflow
    });
    
    // Marcar todos os grupos como colapsados e atualizar contadores
    headers.forEach(header => {
        // Forçar estado colapsado - IMPORTANTE: remover todas as classes de estado
        header.classList.remove('expanded', 'collapsed');
        header.classList.add('collapsed');
        
        const groupId = header.dataset.group;
        if (groupId) {
            updateGroupCounters(groupId);
        }
        
        // Garantir que o ícone esteja na posição correta
        const toggleIcon = header.querySelector('.toggle-icon');
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
    });
    
    // AGORA tornar visível novamente (após tudo estar configurado)
    contentRows.forEach(row => {
        row.style.visibility = 'visible';
    });
    
    tableHeaders.forEach(header => {
        header.style.visibility = 'visible';
    });
    
    console.log('Grupos inicializados com sucesso - todos colapsados');
}

function initializeMobileGroups() {
    console.log('Inicializando grupos mobile...');
    
    const mobileGroups = document.querySelectorAll('.group-card');
    console.log('Grupos mobile encontrados:', mobileGroups.length);
    
    mobileGroups.forEach(group => {
        const groupId = group.dataset.group;
        const content = group.querySelector('.group-card-content');
        const header = group.querySelector('.group-card-header');
        const toggleIcon = header.querySelector('.toggle-icon');
        
        // IMPORTANTE: Ocultar conteúdo ANTES de qualquer manipulação
        if (content) {
            content.style.display = 'none';
            content.style.visibility = 'hidden'; // Adicionar para evitar reflow
        }
        
        // Marcar como colapsado
        group.classList.add('collapsed');
        group.classList.remove('expanded');
        
        // Rotacionar ícone
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
        
        // Atualizar contadores
        if (groupId) {
            updateMobileGroupCounters(groupId);
        }
        
        // AGORA tornar visível novamente
        if (content) {
            content.style.visibility = 'visible';
        }
    });
    
    console.log('Grupos mobile inicializados com sucesso');
}

function toggleGroup(header, groupId) {
    console.log('Toggle grupo:', groupId, 'Header:', header);
    
    const contentRows = document.querySelectorAll('.group-content-row[data-group="' + groupId + '"]');
    const tableHeader = document.querySelector('.group-table-header[data-group="' + groupId + '"]');
    
    if (contentRows.length === 0) {
        console.log('Nenhuma linha de conteúdo encontrada para o grupo:', groupId);
        return;
    }
    
    // Verificar se o grupo está colapsado ou expandido
    const isCollapsed = header.classList.contains('collapsed');
    console.log('Estado atual:', isCollapsed ? 'colapsado' : 'expandido');
    
    if (isCollapsed) {
        // Expandir grupo
        console.log('Expandindo grupo:', groupId);
        if (tableHeader) tableHeader.style.display = 'table-row';
        contentRows.forEach(row => {
            row.style.display = 'table-row';
        });
        header.classList.remove('collapsed');
        header.classList.add('expanded');
        
        // Rotacionar ícone para baixo
        const toggleIcon = header.querySelector('.toggle-icon');
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(0deg)';
        }
    } else {
        // Colapsar grupo
        console.log('Colapsando grupo:', groupId);
        if (tableHeader) tableHeader.style.display = 'none';
        contentRows.forEach(row => {
            row.style.display = 'none';
        });
        header.classList.remove('expanded');
        header.classList.add('collapsed');
        
        // Rotacionar ícone para direita
        const toggleIcon = header.querySelector('.toggle-icon');
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
    }
}

function toggleMobileGroup(header, groupId) {
    const group = header.closest('.group-card');
    const content = group.querySelector('.group-card-content');
    const toggleIcon = header.querySelector('.toggle-icon');
    
    if (!content) return;
    
    const isCollapsed = group.classList.contains('collapsed');
    
    if (isCollapsed) {
        // Expandir grupo
        content.style.display = 'block';
        group.classList.remove('collapsed');
        group.classList.add('expanded');
        
        // Rotacionar ícone para baixo
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(0deg)';
        }
    } else {
        // Colapsar grupo
        content.style.display = 'none';
        group.classList.remove('expanded');
        group.classList.add('collapsed');
        
        // Rotacionar ícone para direita
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
    }
}

function expandAllGroups() {
    const contentRows = document.querySelectorAll('.group-content-row');
    const tableHeaders = document.querySelectorAll('.group-table-header');
    const headers = document.querySelectorAll('.group-header');
    
    contentRows.forEach(row => {
        row.style.display = 'table-row';
    });
    
    tableHeaders.forEach(header => {
        header.style.display = 'table-row';
    });
    
    headers.forEach(header => {
        header.classList.remove('collapsed');
        header.classList.add('expanded');
        
        // Rotacionar ícone para baixo
        const toggleIcon = header.querySelector('.toggle-icon');
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(0deg)';
        }
    });
    
    // Expandir grupos mobile também
    const mobileGroups = document.querySelectorAll('.group-card');
    mobileGroups.forEach(group => {
        const content = group.querySelector('.group-card-content');
        const header = group.querySelector('.group-card-header');
        const toggleIcon = header.querySelector('.toggle-icon');
        
        if (content) {
            content.style.display = 'block';
        }
        group.classList.remove('collapsed');
        group.classList.add('expanded');
        
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(0deg)';
        }
    });
}

function collapseAllGroups() {
    const contentRows = document.querySelectorAll('.group-content-row');
    const tableHeaders = document.querySelectorAll('.group-table-header');
    const headers = document.querySelectorAll('.group-header');
    
    contentRows.forEach(row => {
        row.style.display = 'none';
    });
    
    tableHeaders.forEach(header => {
        header.style.display = 'none';
    });
    
    headers.forEach(header => {
        header.classList.remove('expanded');
        header.classList.add('collapsed');
        
        // Rotacionar ícone para direita
        const toggleIcon = header.querySelector('.toggle-icon');
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
    });
    
    // Colapsar grupos mobile também
    const mobileGroups = document.querySelectorAll('.group-card');
    mobileGroups.forEach(group => {
        const content = group.querySelector('.group-card-content');
        const header = group.querySelector('.group-card-header');
        const toggleIcon = header.querySelector('.toggle-icon');
        
        if (content) {
            content.style.display = 'none';
        }
        group.classList.remove('expanded');
        group.classList.add('collapsed');
        
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
    });
}

function forceCollapseAll() {
    const contentRows = document.querySelectorAll('.group-content-row');
    const tableHeaders = document.querySelectorAll('.group-table-header');
    const headers = document.querySelectorAll('.group-header');
    
    contentRows.forEach(row => {
        row.style.display = 'none';
    });
    
    tableHeaders.forEach(header => {
        header.style.display = 'none';
    });
    
    headers.forEach(header => {
        header.classList.remove('expanded');
        header.classList.add('collapsed');
        
        // Rotacionar ícone para direita
        const toggleIcon = header.querySelector('.toggle-icon');
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
    });
    
    // Forçar colapso dos grupos mobile também
    const mobileGroups = document.querySelectorAll('.group-card');
    mobileGroups.forEach(group => {
        const content = group.querySelector('.group-card-content');
        const header = group.querySelector('.group-card-header');
        const toggleIcon = header.querySelector('.toggle-icon');
        
        if (content) {
            content.style.display = 'none';
        }
        group.classList.remove('expanded');
        group.classList.add('collapsed');
        
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
    });
}

function filterByGroup(searchTerm) {
    if (!searchTerm.trim()) {
        collapseAllGroups();
        return;
    }
    
    const headers = document.querySelectorAll('.group-header');
    let foundGroup = false;
    
    headers.forEach(header => {
        const groupName = header.querySelector('.group-name').textContent.toLowerCase();
        const groupId = header.querySelector('.group-name').closest('tr').dataset.group;
        
        if (groupName.includes(searchTerm.toLowerCase())) {
            const contentRows = document.querySelectorAll('.group-content-row[data-group="' + groupId + '"]');
            const tableHeader = document.querySelector('.group-table-header[data-group="' + groupId + '"]');
            
            contentRows.forEach(row => {
                row.style.display = 'table-row';
            });
            
            if (tableHeader) {
                tableHeader.style.display = 'table-row';
            }
            
            header.classList.remove('collapsed');
            header.classList.add('expanded');
            
            // Rotacionar ícone para baixo
            const toggleIcon = header.querySelector('.toggle-icon');
            if (toggleIcon) {
                toggleIcon.style.transform = 'rotate(0deg)';
            }
            
            if (!foundGroup) {
                header.scrollIntoView({ behavior: 'smooth', block: 'center' });
                foundGroup = true;
            }
        }
    });
    
    // Filtrar grupos mobile também
    const mobileGroups = document.querySelectorAll('.group-card');
    mobileGroups.forEach(group => {
        const groupName = group.querySelector('.group-name').textContent.toLowerCase();
        
        if (groupName.includes(searchTerm.toLowerCase())) {
            const content = group.querySelector('.group-card-content');
            const header = group.querySelector('.group-card-header');
            const toggleIcon = header.querySelector('.toggle-icon');
            
            if (content) {
                content.style.display = 'block';
            }
            group.classList.remove('collapsed');
            group.classList.add('expanded');
            
            if (toggleIcon) {
                toggleIcon.style.transform = 'rotate(0deg)';
            }
            
            if (!foundGroup) {
                group.scrollIntoView({ behavior: 'smooth', block: 'center' });
                foundGroup = true;
            }
        }
    });
}

function togglePermission(checkbox, pageId, groupId) {
    // Esta função agora é chamada pelo toggle switch diretamente
    updateGroupCounters(groupId);
    updateMobileGroupCounters(groupId);
}

function toggleGroupPermissions(checkbox, groupId) {
    const contentRows = document.querySelectorAll('.group-content-row[data-group="' + groupId + '"]');
    // Buscar todos os toggles dentro das linhas de conteúdo
    let permissionToggles = [];
    contentRows.forEach(row => {
        const toggles = row.querySelectorAll('.permission-toggle');
        permissionToggles = permissionToggles.concat(Array.from(toggles));
    });
    
    permissionToggles.forEach(toggle => {
        toggle.checked = checkbox.checked;
    });
    
    updateGroupCounters(groupId);
    updateMobileGroupCounters(groupId);
}

function authorizeGroup(groupId) {
    console.log('Autorizando grupo:', groupId);
    
    // Atualizar permissões desktop
    const contentRows = document.querySelectorAll('.group-content-row[data-group="' + groupId + '"]');
    // Buscar todos os toggles dentro das linhas de conteúdo
    let permissionToggles = [];
    contentRows.forEach(row => {
        const toggles = row.querySelectorAll('.permission-toggle');
        permissionToggles = permissionToggles.concat(Array.from(toggles));
    });
    
    permissionToggles.forEach(toggle => {
        toggle.checked = true;
    });
    
    // Atualizar permissões mobile
    const mobileGroup = document.querySelector('.group-card[data-group="' + groupId + '"]');
    if (mobileGroup) {
        const mobileToggles = mobileGroup.querySelectorAll('.permission-toggle');
        mobileToggles.forEach(toggle => {
            toggle.checked = true;
        });
    }
    
    // Atualizar contadores
    updateGroupCounters(groupId);
    updateMobileGroupCounters(groupId);
    
    console.log('Grupo autorizado com sucesso:', groupId);
}

function revokeGroup(groupId) {
    console.log('Revogando grupo:', groupId);
    
    // Atualizar permissões desktop
    const contentRows = document.querySelectorAll('.group-content-row[data-group="' + groupId + '"]');
    // Buscar todos os toggles dentro das linhas de conteúdo
    let permissionToggles = [];
    contentRows.forEach(row => {
        const toggles = row.querySelectorAll('.permission-toggle');
        permissionToggles = permissionToggles.concat(Array.from(toggles));
    });
    
    permissionToggles.forEach(toggle => {
        toggle.checked = false;
    });
    
    // Atualizar permissões mobile
    const mobileGroup = document.querySelector('.group-card[data-group="' + groupId + '"]');
    if (mobileGroup) {
        const mobileToggles = mobileGroup.querySelectorAll('.permission-toggle');
        mobileToggles.forEach(toggle => {
            toggle.checked = false;
        });
    }
    
    // Atualizar contadores
    updateGroupCounters(groupId);
    updateMobileGroupCounters(groupId);
    
    console.log('Grupo revogado com sucesso:', groupId);
}

function authorizeMobileGroup(groupId) {
    const mobileGroup = document.querySelector('.group-card[data-group="' + groupId + '"]');
    if (mobileGroup) {
        const permissionToggles = mobileGroup.querySelectorAll('.permission-toggle');
        
        permissionToggles.forEach(toggle => {
            toggle.checked = true;
        });
        
        updateMobileGroupCounters(groupId);
        updateGroupCounters(groupId);
    }
}

function revokeMobileGroup(groupId) {
    const mobileGroup = document.querySelector('.group-card[data-group="' + groupId + '"]');
    if (mobileGroup) {
        const permissionToggles = mobileGroup.querySelectorAll('.permission-toggle');
        
        permissionToggles.forEach(toggle => {
            toggle.checked = false;
        });
        
        updateMobileGroupCounters(groupId);
        updateGroupCounters(groupId);
    }
}

function updateGroupCounters(groupId) {
    const header = document.querySelector('.group-header[data-group="' + groupId + '"]');
    if (!header) {
        console.error('Header não encontrado para o grupo:', groupId);
        return;
    }
    
    // CORREÇÃO: contentRows é um NodeList, não um elemento DOM
    const contentRows = document.querySelectorAll('.group-content-row[data-group="' + groupId + '"]');
    // Buscar todos os toggles dentro das linhas de conteúdo
    let permissionToggles = [];
    contentRows.forEach(row => {
        const toggles = row.querySelectorAll('.permission-toggle');
        permissionToggles = permissionToggles.concat(Array.from(toggles));
    });
    const total = permissionToggles.length;
    const allowed = Array.from(permissionToggles).filter(toggle => toggle.checked).length;
    const denied = total - allowed;
    
    const totalCounter = header.querySelector('.group-counter.total');
    const allowedCounter = header.querySelector('.group-counter.allowed');
    const deniedCounter = header.querySelector('.group-counter.denied');
    
    if (totalCounter) {
        totalCounter.textContent = total + ' total';
        console.log('Contador total atualizado:', total + ' total');
    }
    if (allowedCounter) {
        allowedCounter.textContent = allowed;
        console.log('Contador autorizado atualizado:', allowed);
    }
    if (deniedCounter) {
        deniedCounter.textContent = denied;
        console.log('Contador negado atualizado:', denied);
    }
    
    console.log('Contadores atualizados para o grupo:', groupId, 'Total:', total, 'Autorizado:', allowed, 'Negado:', denied);
}

function updateMobileGroupCounters(groupId) {
    const mobileGroup = document.querySelector('.group-card[data-group="' + groupId + '"]');
    if (!mobileGroup) {
        console.error('Grupo mobile não encontrado para:', groupId);
        return;
    }
    
    const permissionToggles = mobileGroup.querySelectorAll('.permission-toggle');
    const total = permissionToggles.length;
    const allowed = Array.from(permissionToggles).filter(toggle => toggle.checked).length;
    const denied = total - allowed;
    
    const totalBadge = mobileGroup.querySelector('.badge.bg-secondary');
    const allowedBadge = mobileGroup.querySelector('.badge.bg-success');
    const deniedBadge = mobileGroup.querySelector('.badge.bg-danger');
    
    if (totalBadge) {
        totalBadge.textContent = total + ' total';
    }
    if (allowedBadge) {
        allowedBadge.textContent = allowed;
    }
    if (deniedBadge) {
        deniedBadge.textContent = denied;
    }
    
    console.log('Contadores mobile atualizados para o grupo:', groupId, 'Total:', total, 'Autorizado:', allowed, 'Negado:', denied);
}

function confirmAndSavePermissions() {
    // Mostrar confirmação
    if (confirm('Deseja realmente atualizar as permissões?')) {
        savePermissions();
    }
}

function savePermissions() {
    console.log('Função savePermissions chamada');
    
    // Selecionar o formulário desktop (mais específico)
    let form = document.querySelector('.d-none.d-md-block form');
    if (!form) {
        console.error('Formulário desktop não encontrado, tentando formulário genérico...');
        const genericForm = document.querySelector('form');
        if (!genericForm) {
            console.error('Nenhum formulário encontrado.');
            showError('Formulário não encontrado. Recarregue a página e tente novamente.');
            return;
        }
        form = genericForm;
    }
    
    console.log('Formulário encontrado:', form);
    console.log('Action do formulário:', form.action);

    const csrf_token = form.querySelector('input[name="csrf_token"]').value;
    const adms_access_level_id = form.querySelector('input[name="adms_access_level_id"]').value;

    console.log('CSRF Token:', csrf_token);
    console.log('Access Level ID:', adms_access_level_id);

    if (!csrf_token) {
        showError('Token CSRF não encontrado. Recarregue a página e tente novamente.');
        return;
    }

    if (!adms_access_level_id) {
        showError('ID do nível de acesso não encontrado.');
        return;
    }

    // Coletar todas as permissões marcadas (tanto desktop quanto mobile)
    const allToggles = document.querySelectorAll('.permission-toggle:checked');
    console.log('Total de toggles marcados:', allToggles.length);
    
    const accessLevelPages = [];
    allToggles.forEach(toggle => {
        const pageId = toggle.dataset.pageId;
        if (pageId) {
            accessLevelPages.push(pageId);
            console.log('Página adicionada:', pageId);
        }
    });

    console.log('Páginas para salvar:', accessLevelPages);

    // Mostrar indicador de carregamento
    const saveButton = document.getElementById('savePermissionsBtn');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    saveButton.disabled = true;

    // Preparar dados para envio
    const formData = new FormData();
    formData.append('csrf_token', csrf_token);
    formData.append('adms_access_level_id', adms_access_level_id);
    
    // Adicionar cada página individualmente para manter como array
    accessLevelPages.forEach(pageId => {
        formData.append('accessLevelPage[]', pageId);
    });
    
    // Converter FormData para URLSearchParams
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        params.append(key, value);
    }

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
    })
    .then(response => {
        console.log('Resposta recebida:', response);
        console.log('Status:', response.status);
        
        // Verificar se a resposta é JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // Se não for JSON, tentar ler como texto para debug
            return response.text().then(text => {
                console.error('Resposta não é JSON:', text);
                throw new Error('Servidor retornou HTML em vez de JSON. Verifique o console para mais detalhes.');
            });
        }
    })
    .then(data => {
        console.log('Dados da resposta:', data);
        if (data.success) {
            showSuccess('Permissões salvas com sucesso!');
            // Recarregar a página após 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showError('Erro ao salvar permissões: ' + (data.message || 'Desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro na requisição AJAX:', error);
        showError('Erro de conexão com o servidor: ' + error.message);
    })
    .finally(() => {
        // Restaurar botão
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

// Função para mostrar alerta de sucesso
function showSuccess(message) {
    const successAlert = document.getElementById('successAlert');
    const successMessage = document.getElementById('successMessage');
    
    successMessage.textContent = message;
    successAlert.style.display = 'block';
    successAlert.classList.add('show');
    
    // Auto-hide após 5 segundos
    setTimeout(() => {
        successAlert.classList.remove('show');
        setTimeout(() => {
            successAlert.style.display = 'none';
        }, 150);
    }, 5000);
}

// Função para mostrar alerta de erro
function showError(message) {
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    
    errorMessage.textContent = message;
    errorAlert.style.display = 'block';
    errorAlert.classList.add('show');
    
    // Auto-hide após 8 segundos
    setTimeout(() => {
        errorAlert.classList.remove('show');
        setTimeout(() => {
            errorAlert.style.display = 'none';
        }, 150);
    }, 8000);
}
