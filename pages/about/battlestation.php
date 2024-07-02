<?php

	use Vegvisir\Path;

	use VLW\Client\API;
	use VLW\API\Endpoints;

	use VLW\API\Databases\VLWdb\Models\Battlestation\{
		MbModel,
		CpuModel,
		GpuModel,
		PsuModel,
		DramModel,
		StorageModel,
		ChassisModel
	};
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\{
		MbPsuModel,
		MbGpuModel,
		MbDramModel,
		ConfigModel,
		MbStorageModel,
		ChassisMbModel,
		MbCpuCoolerModel,
		MbStorageSlotFormfactorEnum
	};

	require_once Path::root("src/client/API.php");
	require_once Path::root("api/src/Endpoints.php");

	// Load hardware database models
	require_once Path::root("api/src/databases/models/Battlestation/Mb.php");
	require_once Path::root("api/src/databases/models/Battlestation/Cpu.php");
	require_once Path::root("api/src/databases/models/Battlestation/Gpu.php");
	require_once Path::root("api/src/databases/models/Battlestation/Psu.php");
	require_once Path::root("api/src/databases/models/Battlestation/Dram.php");
	require_once Path::root("api/src/databases/models/Battlestation/Storage.php");
	require_once Path::root("api/src/databases/models/Battlestation/Chassis.php");

	// Load hardware config database models
	require_once Path::root("api/src/databases/models/Battlestation/Config/MbPsu.php");
	require_once Path::root("api/src/databases/models/Battlestation/Config/MbGpu.php");
	require_once Path::root("api/src/databases/models/Battlestation/Config/MbDram.php");
	require_once Path::root("api/src/databases/models/Battlestation/Config/Config.php");
	require_once Path::root("api/src/databases/models/Battlestation/Config/MbStorage.php");
	require_once Path::root("api/src/databases/models/Battlestation/Config/ChassisMb.php");
	require_once Path::root("api/src/databases/models/Battlestation/Config/MbCpuCooler.php");

	const GIGA = 0x3B9ACA00;
	const MEGA = 0xF4240;

	// Connect to VLW API
	$api = new API();

	$config = $api->call(Endpoints::BATTLESTATION->value)->get();

?>
<style><?= VV::css("pages/about/battlestation") ?></style>
<?php if ($config->ok): ?>
	<?php foreach ($config->json() as $config): ?>

		<?php 

			// Get motherboard details by ref_mb_id from config
			$motherboard = $api->call(Endpoints::BATTLESTATION_MB->value)->params([
				MbModel::ID->value => $config[ChassisMbModel::REF_MB_ID->value]
			])->get()->json()[0];

			$test = true;
		
		?>

		<section class="heading">
			<h1><?= $config[ConfigModel::FRIENDLY_NAME->value] ?? "Lucious" ?></h1>
			<p>This rig was built: <?= date(API::DATE_FORMAT, $config[ConfigModel::DATE_BUILT->value]) ?></p>
		</section>
		<section class="config" 
				data-mb="1"
				data-cpu="<?= count($motherboard["cpus"]) ?>" 
				data-psu="<?= count($motherboard["psus"]) ?>" 
				data-gpu="<?= count($motherboard["gpus"]) ?>"
				data-dram="<?= count($motherboard["dram"]) ?>"
				data-case="<?= count($motherboard["chassis"]) ?>"
				data-drives-mdottwo="<?= count(array_keys(array_column($motherboard["storage"], MbStorageModel::SLOT_FORMFACTOR->value), MbStorageSlotFormfactorEnum::MDOTTWO->value)) ?>"
				data-drives-twodotfive="<?= count(array_keys(array_column($motherboard["storage"], MbStorageModel::SLOT_FORMFACTOR->value), MbStorageSlotFormfactorEnum::TWODOTFIVE->value)) ?>" 
				data-drives-threedotfive="<?= count(array_keys(array_column($motherboard["storage"], MbStorageModel::SLOT_FORMFACTOR->value), MbStorageSlotFormfactorEnum::THREEDOTFIVE->value)) ?>" 
			>
			<?= VV::media("battlestation.svg") ?>
			<div class="specs">

				<?php // Show motherboard details ?>
				<?php if ($motherboard): ?>
					<div vv="battlestation" vv-call="setSpecActive" data-target="mb" class="spec">
						<p>Motherboard</p>
						<h3><?= $motherboard[MbModel::VENDOR_NAME->value] ?> <span><?= $motherboard[MbModel::VENDOR_MODEL->value] ?></span></h3>
						<div>
							<div>
								<label>Formfactor</label>
								<p><?= $motherboard[MbModel::FORMFACTOR->value] ?></p>
							</div>
							<div>
								<label>Brand name</label>
								<p><?= $motherboard[MbModel::VENDOR_NAME->value] ?></p>
							</div>
							<div>
								<label>Brand model</label>
								<p><?= $motherboard[MbModel::VENDOR_MODEL->value] ?></p>
							</div>
							<div>
								<label>LAN</label>
								<p><?= $motherboard[MbModel::NETWORK_ETHERNET->value] ?? "No LAN" ?></p>
							</div>
							<div>
								<label>WLAN</label>
								<p><?= $motherboard[MbModel::NETWORK_WLAN->value] ?? "No WLAN" ?></p>
							</div>
							<div>
								<label>Bluetooth</label>
								<p><?= $motherboard[MbModel::NETWORK_BLUETOOTH->value] ?? "No Bluetooth" ?></p>
							</div>
							<div>
								<label>Aquired</label>
								<p><?= date(API::DATE_FORMAT, $motherboard[MbModel::DATE_AQUIRED->value]) ?></p>
							</div>

							<?php if ($motherboard[MbModel::DATE_RETIRED->value]): ?>
								<div>
									<label>Retired</label>
									<p><?= date(API::DATE_FORMAT, $motherboard[MbModel::DATE_RETIRED->value]) ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php // List all cases (lol) ?>
				<?php foreach ($motherboard["chassis"] as $mb_chassis): ?>

					<?php // Get case details from endpoint by id ?>
					<?php $case = $api->call(Endpoints::BATTLESTATION_CHASSIS->value)->params([
						ChassisModel::ID->value => $mb_chassis[ChassisMbModel::REF_CHASSIS_ID->value]
					])->get()->json()[0]; ?>

					<div vv="battlestation" vv-call="setSpecActive" data-target="case" class="spec">
						<p>Case</p>
						<h3><?= $case[ChassisModel::VENDOR_NAME->value] ?> <span><?= $case[ChassisModel::VENDOR_MODEL->value] ?></span></h3>
						<div>
							<div>
								<label>Brand name</label>
								<p><?= $case[ChassisModel::VENDOR_NAME->value] ?></p>
							</div>
							<div>
								<label>Brand model</label>
								<p><?= $case[ChassisModel::VENDOR_MODEL->value] ?></p>
							</div>
							<div>
								<label>Nº 2.5" slots</label>
								<p><?= $case[ChassisModel::STORAGE_TWOINCHFIVE->value] ?></p>
							</div>
							<div>
								<label>Nº 3.5" slots</label>
								<p><?= $case[ChassisModel::STORAGE_THREEINCHFIVE->value] ?></p>
							</div>
							<div>
								<label>Aquired</label>
								<p><?= date(API::DATE_FORMAT, $case[ChassisModel::DATE_AQUIRED->value]) ?></p>
							</div>

							<?php if ($motherboard[MbModel::DATE_RETIRED->value]): ?>
								<div>
									<label>Retired</label>
									<p><?= date(API::DATE_FORMAT, $case[ChassisModel::DATE_RETIRED->value]) ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>

				<?php // List all CPUs ?>
				<?php foreach ($motherboard["cpus"] as $mb_cpu): ?>

					<?php // Get case details from endpoint by id ?>
					<?php $cpu = $api->call(Endpoints::BATTLESTATION_CPU->value)->params([
						CpuModel::ID->value => $mb_cpu[MbCpuCoolerModel::REF_CPU_ID->value]
					])->get()->json()[0]; ?>

					<div vv="battlestation" vv-call="setSpecActive" data-target="cpu" class="spec">
						<p>CPU</p>
						<h3><?= $cpu[CpuModel::VENDOR_NAME->value] ?> <span><?= $cpu[CpuModel::VENDOR_MODEL->value] ?></span></h3>
						<div>
							<div>
								<label>Brand name</label>
								<p><?= $cpu[CpuModel::VENDOR_NAME->value] ?></p>
							</div>
							<div>
								<label>Brand model</label>
								<p><?= $cpu[CpuModel::VENDOR_MODEL->value] ?></p>
							</div>
							<div>
								<label>Class</label>
								<p><?= $cpu[CpuModel::CPU_CLASS->value] ?></p>
							</div>
							<div>
								<label>Base Clockspeed</label>
								<p><?= $cpu[CpuModel::CLOCK_BASE->value] / GIGA ?>GHz</p>
							</div>
							<div>
								<label>Turbo Clockspeed</label>
								<p><?= $cpu[CpuModel::CLOCK_TURBO->value] / GIGA ?>GHz</p>
							</div>
							<div>
								<label>Nº cores (P/E)</label>
								<p><?= $cpu[CpuModel::CORE_COUNT_PERFORMANCE->value] + $cpu[CpuModel::CORE_COUNT_EFFICIENCY->value] ?> (<?= $cpu[CpuModel::CORE_COUNT_PERFORMANCE->value] ?>/<?= $cpu[CpuModel::CORE_COUNT_EFFICIENCY->value] ?>)</p>
							</div>
							<div>
								<label>Nº total threads</label>
								<p><?= $cpu[CpuModel::CORE_THREADS->value] ?></p>
							</div>
							<div>
								<label>Aquired</label>
								<p><?= date(API::DATE_FORMAT, $cpu[CpuModel::DATE_AQUIRED->value]) ?></p>
							</div>

							<?php if ($motherboard[MbModel::DATE_RETIRED->value]): ?>
								<div>
									<label>Retired</label>
									<p><?= date(API::DATE_FORMAT, $cpu[CpuModel::DATE_RETIRED->value]) ?></p>
								</div>
							<?php endif; ?>
						</div>
						<div>
							<div>
								<label>In motherboard slot number</label>
								<p><?= $mb_cpu[MbCpuCoolerModel::SOCKET->value] ?></p>
							</div>
							<div>
								<label>Motherboard slot type</label>
								<p><?= $mb_cpu[MbCpuCoolerModel::SOCKET_TYPE->value] ?></p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>

				<?php // List all GPUs ?>
				<?php foreach ($motherboard["gpus"] as $mb_gpu): ?>

					<?php // Get case details from endpoint by id ?>
					<?php $gpu = $api->call(Endpoints::BATTLESTATION_GPU->value)->params([
						GpuModel::ID->value => $mb_gpu[MbGpuModel::REF_GPU_ID->value]
					])->get()->json()[0]; ?>

					<div vv="battlestation" vv-call="setSpecActive" data-target="gpu" class="spec">
						<p>GPU</p>
						<h3><?= $gpu[GpuModel::VENDOR_NAME->value] ?> <span><?= $gpu[GpuModel::VENDOR_CHIP_MODEL->value] ?></span></h3>
						<div>
							<div>
								<label>Chip brand name</label>
								<p><?= $gpu[GpuModel::VENDOR_CHIP_NAME->value] ?></p>
							</div>
							<div>
								<label>Chip brand model</label>
								<p><?= $gpu[GpuModel::VENDOR_CHIP_MODEL->value] ?></p>
							</div>
							<div>
								<label>VRAM</label>
								<p><?= $gpu[GpuModel::MEMORY->value] / GIGA ?>GB</p>
							</div>
							<div>
								<label>Brand name</label>
								<p><?= $gpu[GpuModel::VENDOR_NAME->value] ?></p>
							</div>
							<div>
								<label>Brand model</label>
								<p><?= $gpu[GpuModel::VENDOR_MODEL->value] ?></p>
							</div>
							<div>
								<label>Aquired</label>
								<p><?= date(API::DATE_FORMAT, $gpu[GpuModel::DATE_AQUIRED->value]) ?></p>
							</div>

							<?php if ($motherboard[MbModel::DATE_RETIRED->value]): ?>
								<div>
									<label>Retired</label>
									<p><?= date(API::DATE_FORMAT, $gpu[GpuModel::DATE_RETIRED->value]) ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>

				<?php // List all PSUs ?>
				<?php foreach ($motherboard["psus"] as $mb_psu): ?>

					<?php // Get case details from endpoint by id ?>
					<?php $psu = $api->call(Endpoints::BATTLESTATION_PSU->value)->params([
						PsuModel::ID->value => $mb_psu[MbPsuModel::REF_PSU_ID->value]
					])->get()->json()[0]; ?>

					<div vv="battlestation" vv-call="setSpecActive" data-target="psu" class="spec">
						<p>PSU</p>
						<h3><?= $psu[PsuModel::VENDOR_NAME->value] ?> <span><?= $psu[PsuModel::VENDOR_MODEL->value] ?></span> <span><?= $psu[PsuModel::POWER->value] ?>W</span></h3>
						<div>
							<div>
								<label>Power</label>
								<p><?= $psu[PsuModel::POWER->value] ?>W</p>
							</div>
							<div>
								<label>Brand name</label>
								<p><?= $psu[PsuModel::VENDOR_NAME->value] ?></p>
							</div>
							<div>
								<label>Brand model</label>
								<p><?= $psu[PsuModel::VENDOR_MODEL->value] ?></p>
							</div>
							<div>
								<label>Is modular?</label>
								<p><?= $psu[PsuModel::TYPE_MODULAR->value] === "TRUE" ? "Yes" : "No" ?></p>
							</div>
							<div>
								<label>80+ Rating</label>
								<p><?= $psu[PsuModel::EIGHTYPLUS_RATING->value] ?? "None" ?></p>
							</div>
							<div>
								<label>Aquired</label>
								<p><?= date(API::DATE_FORMAT, $psu[PsuModel::DATE_AQUIRED->value]) ?></p>
							</div>

							<?php if ($motherboard[MbModel::DATE_RETIRED->value]): ?>
								<div>
									<label>Retired</label>
									<p><?= date(API::DATE_FORMAT, $psu[PsuModel::DATE_RETIRED->value]) ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>

				<div vv="battlestation" vv-call="toggleGroup" class="group">
					<p>DRAM</p>
					<?= VV::media("icons/chevron.svg") ?>
				</div>

				<div class="collection">
					<?php // List all DRAM ?>
					<?php foreach ($motherboard["dram"] as $mb_dram): ?>

						<?php // Get case details from endpoint by id ?>
						<?php $dram = $api->call(Endpoints::BATTLESTATION_DRAM->value)->params([
							DramModel::ID->value => $mb_dram[MbDramModel::REF_DRAM_ID->value]
						])->get()->json()[0]; ?>

						<div vv="battlestation" vv-call="setSpecActive" data-target="dram" class="spec">
							<p>DRAM - <?= $dram[DramModel::TECHNOLOGY->value] ?></p>
							<h3><?= $dram[DramModel::VENDOR_NAME->value] ?> 
								<span><?= $dram[DramModel::CAPACITY->value] / GIGA ?>GB</span> 
								<span><?= $dram[DramModel::SPEED->value] / MEGA ?>MHz</span> 
							</h3>
							<div>
								<div>
									<label>Capacity</label>
									<p><?= $dram[DramModel::CAPACITY->value] / GIGA ?>GB</p>
								</div>
								<div>
									<label>Speed</label>
									<p><?= $dram[DramModel::SPEED->value] / MEGA ?>MHz</p>
								</div>
								<div>
									<label>Brand name</label>
									<p><?= $dram[DramModel::VENDOR_NAME->value] ?></p>
								</div>
								<div>
									<label>Brand model</label>
									<p><?= $dram[DramModel::VENDOR_MODEL->value] ?></p>
								</div>
								<div>
									<label>Formfactor</label>
									<p><?= $dram[DramModel::FORMFACTOR->value] ?></p>
								</div>
								<div>
									<label>Technology</label>
									<p><?= $dram[DramModel::TECHNOLOGY->value] ?></p>
								</div>
								<div>
									<label>Is ECC?</label>
									<p><?= $dram[DramModel::ECC->value] === "TRUE" ? "Yes" : "No" ?></p>
								</div>
								<div>
									<label>Is buffered?</label>
									<p><?= $dram[DramModel::BUFFERED->value] === "TRUE" ? "Yes" : "No" ?></p>
								</div>
								<div>
									<label>Aquired</label>
									<p><?= date(API::DATE_FORMAT, $dram[DramModel::DATE_AQUIRED->value]) ?></p>
								</div>

								<?php if ($motherboard[MbModel::DATE_RETIRED->value]): ?>
									<div>
										<label>Retired</label>
										<p><?= date(API::DATE_FORMAT, $dram[DramModel::DATE_RETIRED->value]) ?></p>
									</div>
								<?php endif; ?>
							</div>
							<div>
								<div>
									<label>In motherboard slot number</label>
									<p><?= $mb_dram[MbDramModel::SOCKET->value] ?></p>
								</div>
								<div>
									<label>Motherboard slot type</label>
									<p><?= $mb_dram[MbDramModel::SOCKET_TYPE->value] ?></p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<div vv="battlestation" vv-call="toggleGroup" class="group">
					<p>Storage</p>
					<?= VV::media("icons/chevron.svg") ?>
				</div>

				<div class="collection">
					<?php // List all storage ?>
					<?php foreach ($motherboard["storage"] as $mb_storage): ?>

						<?php // Get case details from endpoint by id ?>
						<?php $storage = $api->call(Endpoints::BATTLESTATION_STORAGE->value)->params([
							StorageModel::ID->value => $mb_storage[MbStorageModel::REF_STORAGE_ID->value]
						])->get()->json()[0]; ?>

						<div vv="battlestation" vv-call="setSpecActive" data-target="drive" class="spec">
							<p><?= $storage[StorageModel::DISK_FORMFACTOR->value] ?> <?= $storage[StorageModel::DISK_TYPE->value] ?></p>
							<h3>
								<?= $storage[StorageModel::VENDOR_NAME->value] ?> 
								<span><?= floor($storage[StorageModel::DISK_SIZE->value] / GIGA) ?>GB</span>
							</h3>
							<div>
								<div>
									<label>Type</label>
									<p><?= $storage[StorageModel::DISK_TYPE->value] ?></p>
								</div>
								<div>
									<label>Capacity</label>
									<p><?= floor($storage[StorageModel::DISK_SIZE->value] / GIGA) ?>GB</p>
								</div>
								<div>
									<label>Interface</label>
									<p><?= $storage[StorageModel::DISK_INTERFACE->value] ?></p>
								</div>
								<div>
									<label>Formfactor</label>
									<p><?= $storage[StorageModel::DISK_FORMFACTOR->value] ?></p>
								</div>
								<div>
									<label>Brand name</label>
									<p><?= $storage[StorageModel::VENDOR_NAME->value] ?></p>
								</div>
								<div>
									<label>Brand model</label>
									<p><?= $storage[StorageModel::VENDOR_MODEL->value] ?></p>
								</div>
								<div>
									<label>Aquired</label>
									<p><?= date(API::DATE_FORMAT, $storage[StorageModel::DATE_AQUIRED->value]) ?></p>
								</div>

								<?php if ($motherboard[MbModel::DATE_RETIRED->value]): ?>
									<div>
										<label>Retired</label>
										<p><?= date(API::DATE_FORMAT, $storage[StorageModel::DATE_RETIRED->value]) ?></p>
									</div>
								<?php endif; ?>
							</div>
							<div>
								<div>
									<label>Attatched via interface</label>
									<p><?= $mb_storage[MbStorageModel::INTERFACE->value] ?></p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

			</div>
		</section>
	<?php endforeach; ?>
<?php endif; ?>
<script><?= VV::js("pages/about/battlestation") ?></script>