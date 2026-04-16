export type PdfReadingProgress = {
  version: 1
  type: 'pdf'
  page: number
  zoom?: number
  updatedAt: string
}

export type ScrollReadingProgress = {
  version: 1
  type: 'scroll'
  scrollTop: number
  updatedAt: string
}

export type ReadingProgress = PdfReadingProgress | ScrollReadingProgress

const STORAGE_PREFIX = 'reading-progress'

const buildStorageKey = (fileId: number) => `${STORAGE_PREFIX}:${fileId}`

export const getReadingProgress = (fileId: number): ReadingProgress | null => {
  try {
    const rawValue = localStorage.getItem(buildStorageKey(fileId))

    if (!rawValue) {
      return null
    }

    const parsedValue = JSON.parse(rawValue) as ReadingProgress

    if (!parsedValue || typeof parsedValue !== 'object' || !('type' in parsedValue)) {
      return null
    }

    if (parsedValue.type === 'pdf') {
      const page = Number(parsedValue.page)
      const zoom = Number(parsedValue.zoom)

      if (!Number.isFinite(page) || page < 1) {
        return null
      }

      return {
        version: 1,
        type: 'pdf',
        page: Math.trunc(page),
        zoom: Number.isFinite(zoom) ? zoom : undefined,
        updatedAt: typeof parsedValue.updatedAt === 'string' ? parsedValue.updatedAt : new Date().toISOString(),
      }
    }

    if (parsedValue.type === 'scroll') {
      const scrollTop = Number(parsedValue.scrollTop)

      if (!Number.isFinite(scrollTop) || scrollTop < 0) {
        return null
      }

      return {
        version: 1,
        type: 'scroll',
        scrollTop: Math.round(scrollTop),
        updatedAt: typeof parsedValue.updatedAt === 'string' ? parsedValue.updatedAt : new Date().toISOString(),
      }
    }

    return null
  } catch {
    return null
  }
}

export const savePdfReadingProgress = (fileId: number, page: number, zoom?: number) => {
  try {
    const payload: PdfReadingProgress = {
      version: 1,
      type: 'pdf',
      page: Math.max(1, Math.trunc(page)),
      zoom: Number.isFinite(zoom) ? zoom : undefined,
      updatedAt: new Date().toISOString(),
    }

    localStorage.setItem(buildStorageKey(fileId), JSON.stringify(payload))
  } catch {
    // ignore localStorage write errors
  }
}

export const saveScrollReadingProgress = (fileId: number, scrollTop: number) => {
  try {
    const payload: ScrollReadingProgress = {
      version: 1,
      type: 'scroll',
      scrollTop: Math.max(0, Math.round(scrollTop)),
      updatedAt: new Date().toISOString(),
    }

    localStorage.setItem(buildStorageKey(fileId), JSON.stringify(payload))
  } catch {
    // ignore localStorage write errors
  }
}
