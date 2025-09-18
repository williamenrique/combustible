
			</main>
			<!-- end Main Content -->
		</div>
		<!-- end contenedor total -->

	<script>
	const base_url = "<?= base_url()?>";
	const userId = "<?= $_SESSION['userData']['usuario_id']?>";
	if(document.querySelectorAll('[data-menu]')){
		document.querySelectorAll('[data-menu]').forEach(elemento => {
			const valorDataMenu = elemento.dataset.menu;
			const valorPage = "<?= $data['page_name']?>"
			if (valorDataMenu === valorPage) {
				elemento.classList.add('active')
				elemento.classList.add('open')
				// console.log(`Clase agregada a: ${valorDataMenu}`);
			}
		})
	}
	if(document.querySelectorAll('[data-submenu]')){
		document.querySelectorAll('[data-submenu]').forEach(elemento => {
			const valorDataSubMenu = elemento.dataset.submenu;
			const valorPageSubmenu = "<?= $data['page_link']?>"
			if (valorPageSubmenu === valorDataSubMenu) {
				elemento.classList.add('active')
			}
		})
	}
	</script>
	<!-- <script src="<?= PLUGINS ?>js/jquery.min.js"></script> -->
	<!-- <script src="<?= PLUGINS?>js/sweetalert2@10.js"></script> -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
	<script src="<?= JS ?>function.main.js"></script>
	<script src="<?= JS.$data['page_functions']?>"></script>
	<script src="<?= JS ?>function.imprimir.js"></script>
	<script src="<?= JS ?>notifications.js"></script>
	</body>
</html>