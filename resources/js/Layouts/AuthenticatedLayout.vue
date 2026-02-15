<script setup>
import { router } from '@inertiajs/vue3'
import { PhSignOut, PhUser } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
</script>

<template>
  <div class="min-h-screen bg-background">
	<!-- Top bar -->
	<nav class="border-b border-border">
	  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="flex h-16 items-center justify-between">
		  <a :href="route('dashboard')" class="flex items-center gap-2">
			<svg width="24" height="18" viewBox="0 0 379 265" fill="none" xmlns="http://www.w3.org/2000/svg">
			  <path d="M179.138 265L24.2363 19.7487H0V0H201.618V19.7487H184.407C179.958 19.7487 176.211 21.5858 173.167 25.26C170.357 28.9341 168.952 33.9861 168.952 40.4159C168.952 45.3148 170.123 51.1323 172.464 57.8683C175.04 64.2981 179.138 71.1872 184.758 78.5355L239.553 151.101L278.191 97.3657C282.64 91.2421 285.801 85.2715 287.675 79.4541C289.782 73.3304 290.836 67.3599 290.836 61.5425C290.836 49.9076 286.504 40.1098 277.84 32.149C269.41 23.8821 257.467 19.7487 242.012 19.7487H219.181V0H379V19.7487H359.681L190.378 265H179.138ZM185.812 242.036L196.349 227.34L65.684 19.7487H45.6626L185.812 242.036Z" fill="white"/>
			</svg>
			<span class="text-sm font-semibold tracking-tight text-foreground">The Vault</span>
		  </a>

		  <DropdownMenu>
			<DropdownMenuTrigger asChild>
			  <Button variant="ghost" size="icon" class="h-8 w-8">
				<PhUser class="h-4 w-4 text-foreground" weight="thin" />
			  </Button>
			</DropdownMenuTrigger>
			<DropdownMenuContent align="end">
			  <DropdownMenuItem @click="router.get(route('profile.edit'))">
				Profile
			  </DropdownMenuItem>
			  <DropdownMenuItem @click="router.post(route('logout'))">
				<PhSignOut class="mr-2 h-4 w-4" weight="thin" />
				Log out
			  </DropdownMenuItem>
			</DropdownMenuContent>
		  </DropdownMenu>
		</div>
	  </div>
	</nav>

	<!-- Page Content -->
	<main>
	  <slot />
	</main>
  </div>
</template>
