<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: false })
</script>

<template>
    <Head title="Terms of Service" />

    <div class="min-h-screen bg-background px-4 py-8 sm:py-16">
        <div class="mx-auto max-w-2xl">
            <div class="mb-6 sm:mb-8">
                <h1 class="text-xl font-bold text-foreground sm:text-2xl">Terms of Service</h1>
                <p class="mt-1 text-sm text-muted-foreground">MIT License</p>
            </div>

            <div class="rounded-lg border border-border bg-card p-4 font-mono text-xs leading-relaxed text-muted-foreground sm:p-5 sm:text-sm">
                <p class="mb-4">
                    Permission is hereby granted, free of charge, to any person obtaining a
                    copy of this software and associated documentation files (the
                    "Software"), to deal in the Software without restriction, including
                    without limitation the rights to use, copy, modify, merge, publish,
                    distribute, sublicense, and/or sell copies of the Software, and to
                    permit persons to whom the Software is furnished to do so, subject to
                    the following conditions:
                </p>

                <p class="mb-4">
                    The above copyright notice and this permission notice shall be included
                    in all copies or substantial portions of the Software.
                </p>

                <p class="text-foreground">
                    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
                    OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
                    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
                    IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
                    CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
                    TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
                    SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
                </p>
            </div>

            <div class="mt-6 flex items-center justify-between text-xs text-muted-foreground">
                <Link href="/login" class="hover:text-foreground">Back to login</Link>
                <a
                    href="https://opensource.org/license/MIT"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:text-foreground">
                    View official license
                </a>
            </div>
        </div>
    </div>
</template>
